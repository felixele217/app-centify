export default function positionOfDayInYear(date: Date) {
    const start = new Date(date.getFullYear(), 0, 0)

    const diff = date.getTime() - start.getTime()

    const oneDay = 1000 * 60 * 60 * 24
    const day = Math.floor(diff / oneDay)

    return day
}
