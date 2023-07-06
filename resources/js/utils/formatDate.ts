export default function formatDate(originalDate?: string | Date | null): string {
    if (! originalDate) {
        return '-'
    }

    const date = new Date(originalDate)

    const day = date.getDate();
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const month = monthNames[date.getMonth()];
    const year = date.getFullYear();

    return `${day} ${month} ${year}`;
  }
