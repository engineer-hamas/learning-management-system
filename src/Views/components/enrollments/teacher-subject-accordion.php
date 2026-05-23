<div class="accordion-item">
                                <div class="accordion-header" onclick="toggleAccordion('acc-<?= $index ?>', 'icon-<?= $index ?>')">
                                    <div class="flex-center-gap-15">
                                        <div class="subject-icon-box">
                                            📚
                                        </div>
                                        <div>
                                            <h3 class="subject-title">
                                                <?= e($subject['name']) ?>
                                            </h3>
                                            <span class="subject-meta">CODE: <?= e($subject['code']) ?> &nbsp;&bull;&nbsp; <?= count($subject['enrolled']) ?> Student(s)</span>
                                        </div>
                                    </div>
                                    <div class="flex-center-gap-20">
                                        <a href="/course?id=<?= e($subject['id']) ?>" class="btn-manage-course" onclick="event.stopPropagation();">Manage Course &rarr;</a>
                                        <span class="chevron" id="icon-<?= $index ?>">▼</span>
                                    </div>
                                </div>
                                
                                <div class="accordion-content" id="acc-<?= $index ?>">
                                    <div class="manage-enrollments-header">
                                        <h4 class="manage-title">Manage Enrollments</h4>
                                        <!-- Enroll Student Form with Searchable Dropdown -->
                                        <?php require __DIR__ . '/search-enroll-form.php'; ?>
                                    </div>

                                    <!-- Enrolled Students List -->
                                    <?php if (empty($subject['enrolled'])): ?>
                                        <div class="empty-state">
                                            <div class="empty-icon-lg">👥</div>
                                            No students are currently enrolled in this subject.
                                        </div>
                                    <?php else: ?>
                                        <div class="enrolled-table-container">
                                            <table class="w-full-collapse">
                                                <thead>
                                                    <tr class="table-head-row">
                                                        <th class="table-th-left">Student Name</th>
                                                        <th class="table-th-left">Email Address</th>
                                                        <th class="table-th-left">Enrolled On</th>
                                                        <th class="table-th-right">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($subject['enrolled'] as $stu): ?>
                                                        <tr class="table-body-row">
                                                            <td class="table-td-main">
                                                                <div class="flex-center-gap-12">
                                                                    <div class="table-avatar-fallback">
                                                                        <?= strtoupper(substr(e($stu['name']), 0, 1)) ?>
                                                                    </div>
                                                                    <?= e($stu['name']) ?>
                                                                </div>
                                                            </td>
                                                            <td class="table-td-muted"><?= e($stu['email']) ?></td>
                                                            <td class="table-td-muted"><?= date('M j, Y \a\t g:i A', strtotime($stu['enrolled_at'])) ?></td>
                                                            <td class="table-td-right">
                                                                <form action="/enrollments/remove" method="POST" class="inline-form" onsubmit="return confirm(\'Unenroll student?\');">
                                                                    <input type="hidden" name="subject_id" value="<?= e($subject['id']) ?>">
                                                                    <input type="hidden" name="student_id" value="<?= e($stu['id']) ?>">
                                                                    <button type="submit" class="btn-remove-enrollment">Remove</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>