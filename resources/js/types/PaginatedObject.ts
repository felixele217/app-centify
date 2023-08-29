export default interface PaginatedObject<T = object> {
    current_page: number
    data: Array<T>
    first_page_url: string
    from: number
    last_page: number
    last_page_url: string
    links: Array<{
        active: boolean
        label: string
        url: string
    }>
    next_page_url: string
    path: string
    per_page: number
    prev_page_url: string | null
    to: number
    total: number
}
