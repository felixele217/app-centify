export default function numberOfDaysInQuarter(date: Date): number {
    const currentMonth = date.getMonth()

    if (currentMonth < 3) {
        return 90
    } else if (currentMonth < 6) {
        return 91
    }

    return 92
}
