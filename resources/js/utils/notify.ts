import { notify as notifyFromPackage } from 'notiwind'

export default function notify(
    title: string,
    description: string,
    wasSuccessful: boolean = true,
    timeout: number = 3000
) {
    notifyFromPackage(
        {
            group: 'centify',
            title: title,
            text: description,
            wasSuccessful: wasSuccessful,
        },
        timeout
    )
}
