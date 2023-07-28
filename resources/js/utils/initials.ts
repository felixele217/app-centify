export default function initials(name: string): string
{
    const nameTokens = name.toUpperCase().split(' ')

    return nameTokens[0][0] + nameTokens[nameTokens.length - 1][0];
}
