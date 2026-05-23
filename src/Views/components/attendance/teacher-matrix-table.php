<div class="matrix-container">
                            <table class="matrix-table">
                                <thead>
                                    <tr>
                                        <th class="student-col min-w-250">Student Name</th>
                                        <?php foreach ($attendance_dates as $date): ?>
                                            <th class="min-w-100">
                                                <?= date('M j', strtotime($date)) ?><br>
                                                <span class="date-day-label"><?= date('D', strtotime($date)) ?></span>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $stu): ?>
                                        <tr>
                                            <td class="student-col">
                                                <?= e($stu['name']) ?>
                                                <?php if (!empty($stu['roll_no'])): ?>
                                                    <span class="roll-no-label">(<?= e($stu['roll_no']) ?>)</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php foreach ($attendance_dates as $date): ?>
                                                <td>
                                                    <?php
                                                    $status = $attendance_matrix[$stu['id']][$date] ?? null;
                                                    if ($status === 'present') echo '<span class="status-badge status-present">Present</span>';
                                                    elseif ($status === 'late') echo '<span class="status-badge status-late">Late</span>';
                                                    elseif ($status === 'absent') echo '<span class="status-badge status-absent">Absent</span>';
                                                    else echo '<span class="text-muted-dash">-</span>';
                                                    ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>