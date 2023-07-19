export default function numberOfDaysInQuarter(date: Date) {
    const currentMonth = date.getMonth()

    switch (true) {
        case currentMonth < 3:
            return 90
        case currentMonth < 6:
            return 91
        case currentMonth < 9:
            return 92
        case currentMonth < 12:
            return 92
    }
}
