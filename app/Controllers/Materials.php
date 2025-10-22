<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\EnrollmentModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class Materials extends Controller
{
    protected $session;

    public function __construct()
    {
        $this->session = Services::session();
        helper(['form', 'url']);
    }

    /**
     * Display upload form and handle file upload for a specific course.
     * Method: GET shows form, POST processes upload.
     */
    public function upload(int $course_id)
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $role = strtolower((string) $this->session->get('role'));
        if (!in_array($role, ['admin', 'teacher', 'instructor'], true)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                ->setBody('Forbidden');
        }

        if ($this->request->getMethod() === 'post') {
            $file = $this->request->getFile('material');
            if (!$file || !$file->isValid()) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                    ->setBody('Invalid file upload.');
            }

            // You can adjust allowed mime types/extensions as needed
            $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'png', 'jpg', 'jpeg', 'mp4'];
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, $allowedExtensions, true)) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                    ->setBody('File type not allowed.');
            }

            $uploadDir = WRITEPATH . 'uploads/materials';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0775, true);
            }

            $newName = $file->getRandomName();
            if (!$file->move($uploadDir, $newName)) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                    ->setBody('Failed to move uploaded file.');
            }

            $model = new MaterialModel();
            $insertId = $model->insertMaterial([
                'course_id' => $course_id,
                'file_name' => $file->getClientName(),
                'file_path' => 'uploads/materials/' . $newName, // relative to WRITEPATH
            ]);

            if ($insertId === false) {
                // rollback file
                @unlink($uploadDir . DIRECTORY_SEPARATOR . $newName);
                return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                    ->setBody('Failed to save material record.');
            }

            $this->session->setFlashdata('success', 'Material uploaded successfully.');
            return redirect()->back();
        }

        // Simple minimal form (no separate view to avoid clutter)
        $csrf = csrf_field();
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Material</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h1 class="mb-3">Upload Material (Course ID: {$course_id})</h1>
        {$this->renderFlashMessages()}
        <form method="post" enctype="multipart/form-data" class="card p-3" action="">
            {$csrf}
            <div class="mb-3">
                <label class="form-label">Choose file</label>
                <input type="file" name="material" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</body>
</html>
HTML;
        return $this->response->setBody($html);
    }

    /**
     * Delete a material and its file from storage.
     */
    public function delete(int $material_id)
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $role = strtolower((string) $this->session->get('role'));
        if (!in_array($role, ['admin', 'teacher', 'instructor'], true)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                ->setBody('Forbidden');
        }

        $model = new MaterialModel();
        $material = $model->find($material_id);
        if (!$material) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                ->setBody('Material not found.');
        }

        $fullPath = WRITEPATH . $material['file_path'];
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }

        $model->delete($material_id);
        $this->session->setFlashdata('success', 'Material deleted.');
        return redirect()->back();
    }

    /**
     * Allow download of a material. Students must be enrolled in the course.
     */
    public function download(int $material_id)
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $role = strtolower((string) $this->session->get('role'));
        $userId = (int) ($this->session->get('user_id') ?? 0);

        $model = new MaterialModel();
        $material = $model->find($material_id);
        if (!$material) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                ->setBody('Material not found.');
        }

        $courseId = (int) $material['course_id'];
        $isAllowed = in_array($role, ['admin', 'teacher', 'instructor'], true);

        if (!$isAllowed && $role === 'student') {
            try {
                $enrollModel = new EnrollmentModel();
                // You may have a helper like isEnrolled($userId, $courseId). Fallback to checking list.
                $enrolled = false;
                $enrollments = $enrollModel->getUserEnrollments($userId);
                foreach ($enrollments as $e) {
                    if ((int) ($e['id'] ?? 0) === $courseId) { // assuming 'id' in list corresponds to course id
                        $enrolled = true;
                        break;
                    }
                }
                $isAllowed = $enrolled;
            } catch (\Throwable $e) {
                $isAllowed = false;
            }
        }

        if (!$isAllowed) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                ->setBody('You are not allowed to download this material.');
        }

        $fullPath = WRITEPATH . $material['file_path'];
        if (!is_file($fullPath)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                ->setBody('File not found.');
        }

        return $this->response->download($fullPath, null)->setFileName($material['file_name']);
    }

    private function renderFlashMessages(): string
    {
        $out = '';
        if (session()->getFlashdata('success')) {
            $msg = esc(session()->getFlashdata('success'));
            $out .= "<div class=\"alert alert-success\">{$msg}</div>";
        }
        if (session()->getFlashdata('error')) {
            $msg = esc(session()->getFlashdata('error'));
            $out .= "<div class=\"alert alert-danger\">{$msg}</div>";
        }
        return $out;
    }
}
