export default function paymentCycle(originalDate?: string | null): string {
    if (!originalDate) {
        return '-'
    }

    console.log(originalDate);

    const date = new Date(originalDate)

    const monthNumber = date.getMonth() + 1

    const month = monthNumber < 10
        ? '0' + monthNumber
        : monthNumber

    return `${month}/${date.getFullYear()}`
}
