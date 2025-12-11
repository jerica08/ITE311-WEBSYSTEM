<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Answer Assignment - <?= esc($assignment['title'] ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #e6e3dc;
            font-family: 'Times New Roman', serif;
        }
        .assignment-header {
            background: linear-gradient(135deg, #D1A11F 0%, #DAA520 100%);
            color: #000;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }
        .assignment-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .assignment-meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            font-size: 0.9rem;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .due-date {
            font-weight: 500;
        }
        .due-date.overdue {
            color: #dc3545;
            font-weight: 600;
        }
        .answer-form {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .two-column-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: start;
        }
        .assignment-details {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #DAA520;
            height: fit-content;
        }
        .answer-form-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .section-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #DAA520;
            font-size: 1rem;
        }
        .detail-item {
            margin-bottom: 1rem;
        }
        .detail-label {
            font-weight: 500;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        .detail-value {
            color: #333;
            font-size: 0.95rem;
        }
        .assignment-description {
            color: #555;
            line-height: 1.5;
            font-size: 0.9rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }
        .answer-textarea {
            min-height: 150px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem;
            font-family: 'Times New Roman', serif;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .answer-textarea:focus {
            border-color: #DAA520;
            box-shadow: 0 0 0 0.2rem rgba(218, 165, 32, 0.25);
        }
        .upload-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 2px dashed #dee2e6;
            transition: all 0.2s;
        }
        .upload-section:hover {
            border-color: #DAA520;
            background: #fff;
        }
        .upload-area {
            text-align: center;
            padding: 1rem;
        }
        .upload-icon {
            font-size: 2rem;
            color: #DAA520;
            margin-bottom: 0.5rem;
        }
        .file-input {
            display: none;
        }
        .upload-btn {
            background: #D1A11F;
            color: #000;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .upload-btn:hover {
            background: #DAA520;
            color: #000;
        }
        .file-list {
            margin-top: 1rem;
        }
        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            background: white;
            border-radius: 6px;
            margin-bottom: 0.5rem;
            border: 1px solid #e9ecef;
        }
        .file-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .file-icon {
            color: #DAA520;
            font-size: 1.2rem;
        }
        .remove-file {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
        }
        .remove-file:hover {
            background: #c82333;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }
        .btn-cancel {
            background: transparent;
            color: #666;
            border: 2px solid #666;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .btn-cancel:hover {
            background: #666;
            color: white;
        }
        .btn-submit {
            background: #DAA520;
            color: #000;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .btn-submit:hover {
            background: #D1A11F;
            color: #000;
        }
        .btn-submit:disabled {
            background: #ccc;
            color: #666;
            cursor: not-allowed;
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }
        .warning-box.overdue {
            background: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
    <?= view('templates/header') ?>

    <div class="container my-4">
        <!-- Assignment Header -->
        <div class="card mb-4">
            <div class="assignment-header">
                <div class="assignment-title">
                    <i class="bi bi-pencil-square me-2"></i>
                    Answer Assignment: <?= esc($assignment['title'] ?? '') ?>
                </div>
                <div class="assignment-meta">
                    <div class="meta-item">
                        <i class="bi bi-book"></i>
                        <span><?= esc($course['title'] ?? '') ?> (<?= esc($course['code'] ?? '') ?>)</span>
                    </div>
                    <div class="meta-item">
                        <i class="bi bi-calendar-event"></i>
                        <span class="due-date <?= $isOverdue ? 'overdue' : '' ?>">
                            Due: <?= $dueDate ? date('M j, Y g:i A', strtotime($dueDate)) : 'No due date' ?>
                        </span>
                    </div>
                    <div class="meta-item">
                        <i class="bi bi-trophy"></i>
                        <span><?= esc($assignment['max_score'] ?? '100') ?> points</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="two-column-layout">
            <!-- Left Column: Assignment Details -->
            <div class="assignment-details">
                <h5 class="section-title">
                    <i class="bi bi-file-text me-2"></i>Assignment Details
                </h5>
                
                <div class="detail-item">
                    <div class="detail-label">Course</div>
                    <div class="detail-value"><?= esc($course['title'] ?? '') ?> (<?= esc($course['code'] ?? '') ?>)</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Due Date</div>
                    <div class="detail-value due-date <?= $isOverdue ? 'overdue' : '' ?>">
                        <?= $dueDate ? date('M j, Y g:i A', strtotime($dueDate)) : 'No due date' ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Points</div>
                    <div class="detail-value"><?= esc($assignment['max_score'] ?? '100') ?> points</div>
                </div>
                
                <div class="assignment-description">
                    <?= nl2br(esc($assignment['description'] ?? 'No description provided.')) ?>
                </div>
            </div>

            <!-- Right Column: Answer Form -->
            <div class="answer-form-section">
                <?php if ($isOverdue): ?>
                    <div class="warning-box overdue">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This assignment is overdue! Submit as soon as possible.
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= site_url('student/submitAssignment') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="assignment_id" value="<?= esc($assignment['id'] ?? '') ?>">
                    <input type="hidden" name="course_id" value="<?= esc($course['id'] ?? '') ?>">

                    <!-- Text Answer Section -->
                    <div class="mb-4">
                        <h5 class="section-title">
                            <i class="bi bi-pencil me-2"></i>Your Answer
                        </h5>
                        <textarea name="answer_text" class="form-control answer-textarea" 
                                  placeholder="Write your answer here..." required></textarea>
                        <small class="text-muted">Provide a detailed answer to the assignment above.</small>
                    </div>

                    <!-- File Upload Section -->
                    <div class="mb-4">
                        <h5 class="section-title">
                            <i class="bi bi-paperclip me-2"></i>Attachments (Optional)
                        </h5>
                        <div class="upload-section">
                            <div class="upload-area">
                                <div class="upload-icon">
                                    <i class="bi bi-cloud-upload"></i>
                                </div>
                                <p class="mb-2">Drop files here or click to browse</p>
                                <p class="text-muted small mb-3">
                                    Supported formats: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG (Max size: 10MB)
                                </p>
                                <input type="file" name="attachment" class="file-input" id="fileInput" 
                                       accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                                <button type="button" class="upload-btn" onclick="document.getElementById('fileInput').click()">
                                    <i class="bi bi-folder2-open me-1"></i>Choose Files
                                </button>
                            </div>
                            <div id="fileList" class="file-list"></div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="<?= site_url('student/course/' . ($course['id'] ?? '') . '/assignments') ?>" 
                           class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <i class="bi bi-send me-1"></i>Submit Answer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('fileInput');
            const fileList = document.getElementById('fileList');
            const submitBtn = document.getElementById('submitBtn');

            // Handle file selection
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file size (10MB max)
                    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                    if (file.size > maxSize) {
                        alert('File size must be less than 10MB');
                        fileInput.value = '';
                        return;
                    }

                    // Display selected file
                    displayFile(file);
                }
            });

            // Handle drag and drop
            const uploadSection = document.querySelector('.upload-section');
            
            uploadSection.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadSection.style.borderColor = '#DAA520';
                uploadSection.style.background = '#fff';
            });

            uploadSection.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadSection.style.borderColor = '#dee2e6';
                uploadSection.style.background = '#f8f9fa';
            });

            uploadSection.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadSection.style.borderColor = '#dee2e6';
                uploadSection.style.background = '#f8f9fa';

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    displayFile(files[0]);
                }
            });
        });

        function displayFile(file) {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'file-info';
            
            const fileIcon = document.createElement('i');
            fileIcon.className = 'bi bi-file-earmark file-icon';
            
            const fileName = document.createElement('span');
            fileName.textContent = file.name;
            
            const fileSize = document.createElement('span');
            fileSize.className = 'text-muted small ms-2';
            fileSize.textContent = formatFileSize(file.size);
            
            fileInfo.appendChild(fileIcon);
            fileInfo.appendChild(fileName);
            fileInfo.appendChild(fileSize);
            
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-file';
            removeBtn.textContent = 'Remove';
            removeBtn.onclick = function() {
                fileItem.remove();
                fileInput.value = '';
            };
            
            fileItem.appendChild(fileInfo);
            fileItem.appendChild(removeBtn);
            fileList.appendChild(fileItem);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Handle form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Submitting...';
            
            // Form will submit normally
        });
    </script>
</body>
</html>
