<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class StudentController extends BaseController
{
    public function dashboard()
    {
        // Authorization: must be logged in and student
        if (!session()->get('logged_in') || session()->get('user_role') !== 'student') {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $studentId = (int) session()->get('user_id');
        $tables = $db->listTables();

        // Enrolled courses: join enrollments -> courses if available, otherwise list some courses (fallback)
        $enrolledCourses = [];
        $hasCourses = \is_array($tables) && \in_array('courses', $tables, true);
        $hasEnrollments = \is_array($tables) && \in_array('enrollments', $tables, true);
        if ($hasCourses && $hasEnrollments && $studentId > 0) {
            $enrolledCourses = $db->table('enrollments e')
                ->select('c.id, c.title, c.name, c.description, t.name as teacher_name')
                ->join('courses c', 'c.id = e.course_id', 'left')
                ->join('users t', 't.id = c.teacher_id', 'left')
                ->where('e.user_id', $studentId)
                ->orderBy('c.title', 'ASC')
                ->get()
                ->getResultArray();
        } elseif ($hasCourses) {
            // Fallback: show any available courses
            $enrolledCourses = $db->table('courses')
                ->select('id, title, name, description')
                ->limit(6)
                ->get()
                ->getResultArray();
        }

        // Upcoming deadlines: try assignments table
        $upcomingDeadlines = [];
        $hasAssignments = \is_array($tables) && \in_array('assignments', $tables, true);
        if ($hasAssignments) {
            $builder = $db->table('assignments a')
                ->select('a.due_date, a.title, a.type, c.title as course_title, c.name as course_name');
            if ($hasEnrollments && $studentId > 0) {
                $builder->join('enrollments e', 'e.course_id = a.course_id', 'left')
                        ->where('e.user_id', $studentId);
            }
            if ($hasCourses) {
                $builder->join('courses c', 'c.id = a.course_id', 'left');
            }
            $upcomingDeadlines = $builder
                ->where('a.due_date >=', date('Y-m-d'))
                ->orderBy('a.due_date', 'ASC')
                ->limit(10)
                ->get()
                ->getResultArray();
            // Normalize keys for the view
            $upcomingDeadlines = array_map(function ($d) {
                return [
                    'due_date' => $d['due_date'] ?? '',
                    'course'   => $d['course_title'] ?? ($d['course_name'] ?? ''),
                    'title'    => $d['title'] ?? '',
                    'type'     => $d['type'] ?? 'Task',
                ];
            }, $upcomingDeadlines);
        }

        // Recent grades / feedback: try grades table
        $recentGrades = [];
        $hasGrades = \is_array($tables) && \in_array('grades', $tables, true);
        if ($hasGrades && $studentId > 0) {
            $builder = $db->table('grades g')
                ->select('g.created_at, g.grade, g.feedback, a.title as item_title, c.title as course_title, c.name as course_name')
                ->where('g.user_id', $studentId)
                ->orderBy('g.created_at', 'DESC')
                ->limit(10);
            if ($hasAssignments) {
                $builder->join('assignments a', 'a.id = g.assignment_id', 'left');
            }
            if ($hasCourses) {
                $builder->join('courses c', 'c.id = g.course_id', 'left');
            }
            $rows = $builder->get()->getResultArray();
            $recentGrades = array_map(function ($r) {
                return [
                    'date'     => $r['created_at'] ?? '',
                    'course'   => $r['course_title'] ?? ($r['course_name'] ?? ''),
                    'item'     => $r['item_title'] ?? '',
                    'grade'    => $r['grade'] ?? '',
                    'feedback' => $r['feedback'] ?? '',
                ];
            }, $rows);
        }

        $data = [
            'user' => [
                'id'    => session()->get('user_id'),
                'name'  => session()->get('user_name'),
                'email' => session()->get('user_email'),
                'role'  => session()->get('user_role'),
            ],
            'enrolledCourses'  => $enrolledCourses,
            'upcomingDeadlines' => $upcomingDeadlines,
            'recentGrades'     => $recentGrades,
        ];

        // Render student-specific dashboard view
        return view('student/dashboard', $data);
    }
}
