import initials from '@/utils/StringManipulation/initials'
import { expect, test } from 'vitest'

test('gets initials correctly', () => {
    expect(initials('Felix Droese')).toBe('FD')
    expect(initials('alex dosse')).toBe('AD')
    expect(initials('Gregory Henry Paul')).toBe('GP')
})
