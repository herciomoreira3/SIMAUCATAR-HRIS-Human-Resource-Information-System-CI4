# Plan for Attendance System Enhancement

## Current Problem
- When an employee doesn't clock in, the `attendance:mark-absent` command immediately marks them as "Falta" (Absent)
- Employees can't clock out if they forgot to clock in, and vice versa

## User Requirements
1. Allow employees to clock out even if they didn't clock in (and vice versa)
2. Add a new attendance status: "Setengah Loron" (Half Day) for employees who only do one of clock in or clock out
3. Two half days count as one full day of "Falta"
4. After three full days of "Falta", automatically add the employee to "Sansaun" (Sanction)

## Implementation Steps
1. **Update Database Migration** (if needed): Modify the `prezensa` table's `estadu_prezensa` ENUM to add 'Setengah Loron'
2. **Modify `Funsionariu` Controller**: Update `clockIn()` and `clockOut()` to allow each action even if the other is missing
3. **Modify `MarkAbsent` Command**:
   - Don't immediately mark as "Falta" if only one of clock in/out is missing
   - Mark as "Setengah Loron" if only one is present
   - Track half-day counts
   - Convert two half days to one full "Falta"
   - After three full "Falta" days, add to "Sansaun"
4. **Update Views** (if needed): Show the new "Setengah Loron" status in attendance history
5. **Test the changes**: Verify that all new features work as expected

## Files to Modify
- `app/Database/Migrations/2026-05-15-012231_HrisSchema.php` (or create a new migration)
- `app/Controllers/Funsionariu.php`
- `app/Commands/MarkAbsent.php`
- `app/Views/pages/funsionariu/prezensa.php` (if needed)
- `app/Views/pages/administrador/prezensa.php` (if exists)
