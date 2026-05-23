<div class="summary-cards-grid">
  <!-- Primary Card -->
  <div class="summary-card-primary">
    <div class="summary-card-label">Attendance Rate</div>
    <div class="summary-card-value"><?= $attendance_summary['percentage'] ?? 100 ?>%</div>
  </div>
  
  <!-- Secondary Card -->
  <div class="summary-card-secondary">
    <div class="summary-card-label-secondary">Total Attended</div>
    <div class="summary-card-value-secondary"><?= ($attendance_summary['present'] ?? 0) + ($attendance_summary['late'] ?? 0) ?></div>
  </div>
  
  <!-- Tertiary Card -->
  <div style="background: #fcfcfc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; text-align: center;">
    <div class="summary-card-label-secondary">Total Absent</div>
    <div style="font-size: 2rem; font-weight: 600; color: #dc2626;"><?= $attendance_summary['absent'] ?? 0 ?></div>
  </div>
</div>