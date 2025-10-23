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
        $isLogged = (bool) ($this->session->get('isLoggedIn') ?? $this->session->get('logged_in') ?? false);
        if (!$isLogged) {
            log_message('warning', 'Materials::upload auth redirect (not logged in) uri={uri}', ['uri' => (string) $this->request->getUri()]);
            return redirect()->to('/auth/login');
        }

        $role = strtolower((string) ($this->session->get('role') ?? $this->session->get('user_role') ?? ''));
        log_message('info', 'Materials::upload entry course_id={cid} role={role} method={method} uri={uri}', [
            'cid' => $course_id,
            'role' => $role,
            'method' => $this->request->getMethod(),
            'uri' => (string) $this->request->getUri(),
        ]);
        if (!in_array($role, ['admin', 'teacher', 'instructor'], true)) {
            log_message('warning', 'Materials::upload forbidden for role={role}', ['role' => $role]);
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                ->setBody('Forbidden');
        }

        if ($this->request->getMethod(true) === 'POST') {
            log_message('info', 'Materials::upload handling POST for course_id={cid}', ['cid' => $course_id]);
            $file = $this->request->getFile('material');
            if (!$file || !$file->isValid()) {
                $this->session->setFlashdata('error', 'Invalid file upload.');
                log_message('error', 'Materials::upload invalid file: {error}', ['error' => $file ? $file->getErrorString() : 'no file']);
                return redirect()->back();
            }

            // You can adjust allowed mime types/extensions as needed
            $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'png', 'jpg', 'jpeg', 'mp4'];
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, $allowedExtensions, true)) {
                $this->session->setFlashdata('error', 'File type not allowed.');
                log_message('error', 'Materials::upload disallowed extension: {ext}', ['ext' => $ext]);
                return redirect()->back();
            }

            $uploadDir = WRITEPATH . 'uploads/materials';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0775, true);
                log_message('info', 'Materials::upload created upload dir {dir}', ['dir' => $uploadDir]);
            }

            $newName = $file->getRandomName();
            if (!$file->move($uploadDir, $newName)) {
                $this->session->setFlashdata('error', 'Failed to move uploaded file.');
                log_message('error', 'Materials::upload failed to move file to {dir}', ['dir' => $uploadDir]);
                return redirect()->back();
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
                $err = method_exists($model, 'errors') ? json_encode($model->errors()) : 'unknown';
                log_message('error', 'Materials::upload DB insert failed. errors={err}', ['err' => $err]);
                $this->session->setFlashdata('error', 'Failed to save material record.');
                return redirect()->back();
            }

            log_message('info', 'Materials::upload success course_id={cid} insert_id={id} stored={path}', [
                'cid' => $course_id,
                'id' => $insertId,
                'path' => 'uploads/materials/' . $newName,
            ]);
            $this->session->setFlashdata('success', 'Material uploaded successfully.');
            return redirect()->back();
        }

        // Render themed view
        return view('materials/upload', [
            'course_id' => $course_id,
        ]);
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
