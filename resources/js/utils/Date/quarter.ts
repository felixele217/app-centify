export default function quarter(date: Date): number {
    switch (date.getMonth()) {
        case 0:
        case 1:
        case 2:
            return 1

        case 3:
        case 4:
        case 5:
            return 2

        case 6:
        case 7:
        case 8:
            return 3

        case 9:
        case 10:
        case 11:
            return 4

        default:
            return -1
    }
}
