export default function formatTime(originalDate?: Date | null): string {
    if (!originalDate) {
        return '-'
    }

    const date = new Date(originalDate)

    const day = date.getDate()
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    const month = monthNames[date.getMonth()]
    const year = date.getFullYear()

    const hours = date.getHours().toString().length > 1 ? date.getHours() : '0' + date.getHours()

    const minutes = date.getMinutes().toString().length > 1 ? date.getMinutes() : '0' + date.getMinutes()

    return `${day} ${month} ${year} ${hours}:${minutes}`
}
