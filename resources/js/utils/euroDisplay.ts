// TODO tailwindToHex und euroDisplay testen

export default function euroDisplay(number?: number): string {
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