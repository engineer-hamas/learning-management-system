<table class="roster-table">
                            <thead>
                                <tr class="roster-tr-head">
                                    <th class="roster-th">Student</th>
                                    <th class="roster-th-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($enrolled_students as $student): ?>
                                <tr class="roster-tr">
                                    <td class="roster-td">
                                        <div class="student-row-flex">
                                            <?php if (!empty($student['avatar_path'])): ?>
                                                <img src="<?= e($student['avatar_path']) ?>" alt="Avatar" class="student-avatar">
                                            <?php else: ?>
                                                <div class="student-avatar-fallback">
                                                    <?= strtoupper(substr($student['name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong class="student-name"><?= e($student['name']) ?></strong>
                                                <?php if (!empty($student['roll_no'])): ?>
                                                    <span class="student-roll">Roll No: <?= e($student['roll_no']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="roster-td-center">
                                        <div class="radio-group-container">
                                            <label class="radio-label radio-present">
                                                <input type="radio" name="attendance[<?= $student['id'] ?>]" value="present" checked required> <span class="text-success">Present</span>
                                            </label>
                                            <label class="radio-label radio-late">
                                                <input type="radio" name="attendance[<?= $student['id'] ?>]" value="late" required> <span class="text-warning">Late</span>
                                            </label>
                                            <label class="radio-label radio-absent">
                                                <input type="radio" name="attendance[<?= $student['id'] ?>]" value="absent" required> <span class="text-danger">Absent</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>