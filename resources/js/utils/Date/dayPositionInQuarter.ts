export default function dayPositionInQuarter(date: Date) {
    const quarter = Math.floor((date.getMonth() / 3));

    const firstDayOfQuarter = new Date(date.getFullYear(), quarter * 3, 1);

    const differenceInTime = date.getTime() - firstDayOfQuarter.getTime();
    const differenceInDays = Math.ceil(differenceInTime / (1000 * 3600 * 24));
   
    return differenceInDays;
}
