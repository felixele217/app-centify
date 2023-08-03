export type MarkedRange = {
    start_date: Date
    end_date: Date
    color: 'yellow' | 'green'
}

type RangeObject = {
    start_date: Date
    end_date: Date
}

export default function markedRangesFromRangeObjects<T extends RangeObject>(rangeObjects: Array<T>, color: string) {
    return rangeObjects.map(
        (rangeObject) =>
            ({
                start_date: new Date(rangeObject.start_date),
                end_date: new Date(rangeObject.end_date),
                color: color,
            } as MarkedRange)
    )
}
