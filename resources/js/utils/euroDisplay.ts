export default function euroDisplay(number: number | null, fractionDigitCount = 2): string {
    if (!number) {
        return '0€'
    }

    return (
        Number(number / 100).toLocaleString('de', {
            maximumFractionDigits: fractionDigitCount,
            minimumFractionDigits: fractionDigitCount,
        }) + '€'
    )
}
