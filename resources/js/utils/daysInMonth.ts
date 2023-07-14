export default function daysInMonth(date?: Date): number {
    const currentDate = date || new Date()

    return new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate()
}
