export default function queryParamValue(key: string): string {
    const value = new URLSearchParams(window.location.search).get(key)

    return value ?? ''
}
