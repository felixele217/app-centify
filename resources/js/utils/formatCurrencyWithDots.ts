export default function formatCurrencyWithDots(input: string): string {
    if (!input) {
        return '0'
    }

    const reversedChars = input.split('').reverse()

    const formattedChunks: string[] = []
    for (let i = 0; i < reversedChars.length; i += 3) {
        formattedChunks.push(
            reversedChars
                .slice(i, i + 3)
                .reverse()
                .join('')
        )
    }

    return formattedChunks.reverse().join('.')
}
