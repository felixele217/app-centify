import CurrencyInput from '@/Components/Form/CurrencyInput.vue'
import { fireEvent } from '@testing-library/dom'
import { mount } from '@vue/test-utils'
import { describe, expect, it } from 'vitest'

// @vitest-environment jsdom

describe('CurrencyInput displays correct values', () => {
    const wrapper = mount(CurrencyInput)

    const inputElement = wrapper.find('input')
    it('default is correct', async () => {
        expect(inputElement.element.value).toBe('0,00€')
    })

    it('deleting the last and only character sets the input to the default', async () => {
        await fireEvent.input(inputElement.element, { target: { value: ',00€' } })
        expect(inputElement.element.value).toBe('0,00€')

        await fireEvent.input(inputElement.element, { target: { value: '0,00' } })
        expect(inputElement.element.value).toBe('0,00€')
    })

    it('currency format is correct', async () => {
        await fireEvent.input(inputElement.element, { target: { value: '12,00€3' } })
        expect(inputElement.element.value).toBe('123,00€')

        await fireEvent.input(inputElement.element, { target: { value: '123,00€4' } })
        expect(inputElement.element.value).toBe('1.234,00€')

        await fireEvent.input(inputElement.element, { target: { value: '12345,00€6' } })
        expect(inputElement.element.value).toBe('123.456,00€')

        await fireEvent.input(inputElement.element, { target: { value: '123456,00€7' } })
        expect(inputElement.element.value).toBe('1.234.567,00€')
    })

    it('deleting the comma does not change the value', async () => {
        await fireEvent.input(inputElement.element, { target: { value: '000€' } })
        expect(inputElement.element.value).toBe('0,00€')

        await fireEvent.input(inputElement.element, { target: { value: '15100€' } })
        expect(inputElement.element.value).toBe('151,00€')
    })

    it('adding a zero at the last position adds it as last digit before the comma', async () => {
        await fireEvent.input(inputElement.element, { target: { value: '1,00€0' } })
        expect(inputElement.element.value).toBe('10,00€')

        await fireEvent.input(inputElement.element, { target: { value: '1.155,00€0' } })
        expect(inputElement.element.value).toBe('11.550,00€')
    })

    it('adding a number at the last position adds it as last digit before the comma', async () => {
        await fireEvent.input(inputElement.element, { target: { value: '10,00€5' } })
        expect(inputElement.element.value).toBe('105,00€')

        await fireEvent.input(inputElement.element, { target: { value: '11,00€1' } })
        expect(inputElement.element.value).toBe('111,00€')

        await fireEvent.input(inputElement.element, { target: { value: '0,00€3' } })
        expect(inputElement.element.value).toBe('3,00€')
    })

    it('adding a number at a specific position adds it there', async () => {
        await fireEvent.input(inputElement.element, { target: { value: '105,00€' } })
        expect(inputElement.element.value).toBe('105,00€')

        await fireEvent.input(inputElement.element, { target: { value: '121,00€' } })
        expect(inputElement.element.value).toBe('121,00€')

        await fireEvent.input(inputElement.element, { target: { value: '3151,00€' } })
        expect(inputElement.element.value).toBe('3.151,00€')

        await fireEvent.input(inputElement.element, { target: { value: '3.1051,00€' } })
        expect(inputElement.element.value).toBe('31.051,00€')
    })

    it('deleting at the last position removes the last character', async () => {
        await fireEvent.input(inputElement.element, { target: { value: '10,00' } })
        expect(inputElement.element.value).toBe('1,00€')

        await fireEvent.input(inputElement.element, { target: { value: '15,00' } })
        expect(inputElement.element.value).toBe('1,00€')
    })

    it('adding a non-numeric character does not change the display value', async () => {
        ;['a', 'b', 'c', 'd', 'z', '-', '/', ',', ';', ':', '>', '!', '?'].forEach(async (character) => {
            await fireEvent.input(inputElement.element, { target: { value: '1,00€' + character } })
            expect(inputElement.element.value).toBe('1,00€')
        })
    })
})
