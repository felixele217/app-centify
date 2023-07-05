export default function euroDisplay(number: number | null): string {
    if (!number) {
        return '-'
    }

    return (
        Number(number / 100).toLocaleString('de', {
            maximumFractionDigits: 2,
            minimumFractionDigits: 2,
        }) + '€'
    )
}
