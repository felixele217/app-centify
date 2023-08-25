export default function insertNewLines(text: string, afterWords: number = 15): string {
    const strings = text.split(' ')
    let result = ''

    for (let i = 0; i < strings.length; i++) {
        if ((i + 1) % afterWords === 0) {
            result += strings[i] + '\n'
        } else {
            result += strings[i] + ' '
        }
    }

    return result.trim()
}
