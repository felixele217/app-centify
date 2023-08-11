export default function payoutCycle(originalDate?: string | null | Date): string {
    if (!originalDate) {
        return '-'
    }

    const date = new Date(originalDate)

    const monthNumber = date.getMonth() + 1

    const month = monthNumber < 10 ? '0' + monthNumber : monthNumber

    return `${month}/${date.getFullYear()}`
}
