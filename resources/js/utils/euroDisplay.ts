export default function euroDisplay(number: number | null): string {
    if (!number) {
        return '0€'
    }

    return (
        Number(number / 100).toLocaleString('de', {
            maximumFractionDigits: 2,
            minimumFractionDigits: 2,
        }) + '€'
    )
}
