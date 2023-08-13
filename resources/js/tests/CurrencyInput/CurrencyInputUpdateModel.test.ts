import CurrencyInput from '@/Components/Form/CurrencyInput.vue'
import { fireEvent } from '@testing-library/dom'
import { DOMWrapper, VueWrapper, mount } from '@vue/test-utils'
import { beforeEach, describe, expect, it } from 'vitest'

// @vitest-environment jsdom

describe('CurrencyInput emits correct update events', () => {
    let wrapper: VueWrapper

    let inputElement: DOMWrapper<HTMLInputElement>

    beforeEach(() => {
        wrapper = mount(CurrencyInput)

        inputElement = wrapper.find('input')
    })

    it('deleting the last and only character emits with 0', async () => {
        await fireEvent.input(inputElement.element, { target: { value: ',00€' } })

        const updateEvent = wrapper.emitted('update:modelValue')

        expect(updateEvent![0][0]).toEqual(0)
    })

    it('deleting the comma emits same value', async () => {
        await fireEvent.input(inputElement.element, { target: { value: '1000€' } })
        const updateEvent = wrapper.emitted('update:modelValue')
        expect(updateEvent![0][0]).toEqual(1000)
    })

    it('deleting the zeros after the comma does not emit', async () => {
        await fireEvent.input(inputElement.element, { target: { value: '100,0€' } })
        const updateEvent = wrapper.emitted('update:modelValue')
        expect(updateEvent).toBeFalsy()
    })

    it('adding a zero at the last position adds it as last digit before the comma', async () => {
        await fireEvent.input(inputElement.element, { target: { value: '10,00€0' } })
        let updateEvent = wrapper.emitted('update:modelValue')
        expect(updateEvent![0][0]).toEqual(10000)

        await fireEvent.input(inputElement.element, { target: { value: '1.155,00€0' } })
        updateEvent = wrapper.emitted('update:modelValue')
        expect(updateEvent![1][0]).toEqual(1155000)
    })

    it('adding a number at the last position adds it as last digit before the comma', async () => {
        await fireEvent.input(inputElement.element, { target: { value: '10,00€5' } })
        let updateEvent = wrapper.emitted('update:modelValue')
        expect(updateEvent![0][0]).toEqual(10500)

        await fireEvent.input(inputElement.element, { target: { value: '11,00€1' } })
        updateEvent = wrapper.emitted('update:modelValue')
        expect(updateEvent![1][0]).toEqual(11100)

        await fireEvent.input(inputElement.element, { target: { value: '0,00€3' } })
        updateEvent = wrapper.emitted('update:modelValue')
        expect(updateEvent![2][0]).toEqual(300)
    })

    it('adding a number at a specific position emits the correct event', async () => {
        await fireEvent.input(inputElement.element, { target: { value: '105,00€' } })
        let updateEvent = wrapper.emitted('update:modelValue')
        expect(updateEvent![0][0]).toEqual(10500)

        await fireEvent.input(inputElement.element, { target: { value: '121,00€' } })
        updateEvent = wrapper.emitted('update:modelValue')
        expect(updateEvent![1][0]).toEqual(12100)

        await fireEvent.input(inputElement.element, { target: { value: '3151,00€' } })
        updateEvent = wrapper.emitted('update:modelValue')
        expect(updateEvent![2][0]).toEqual(315100)

        await fireEvent.input(inputElement.element, { target: { value: '3.1051,00€' } })
        updateEvent = wrapper.emitted('update:modelValue')
        expect(updateEvent![3][0]).toEqual(3105100)
    })

    it('deleting at the last position removes the last character', async () => {
        await fireEvent.input(inputElement.element, { target: { value: '10,00' } })
        let updateEvent = wrapper.emitted('update:modelValue')
        expect(updateEvent![0][0]).toEqual(100)

        await fireEvent.input(inputElement.element, { target: { value: '25,00' } })
        updateEvent = wrapper.emitted('update:modelValue')
        expect(updateEvent![1][0]).toEqual(200)
    })

    it('adding a non-numeric character does not emit', async () => {
        ;['a', 'b', 'c', 'd', 'z', '-', '/', ',', ';', ':', '>', '!', '?'].forEach(async (character) => {
            await fireEvent.input(inputElement.element, { target: { value: '1,00€' + character } })
            const updateEvent = wrapper.emitted('update:modelValue')
            expect(updateEvent).toBeFalsy()
        })
    })
})
