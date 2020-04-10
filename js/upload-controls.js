/**
 * Binding a series of interactions 
 * to the upload UI accordingly
 *
 * @author Samuel Guebo <@samuelguebo>
 * @version 0.1
 * Copyright 2020
 */
const supportedFormats = () => {
    return ['image/png', 'image/svg', '']
}
const uploadArea = () => {
    let uploadArea = document.getElementById("upload-area");

    // Trigger the drop event 
    uploadArea.addEventListener('drop', (e) => {
        console.log('entered onDrop')

        let data = e.dataTransfer;
        let files = data.files ? data.files : data.items
        handleUpload(files)

        // Disable default behavior
        e.preventDefault();

    })

    // Trigger inputUpload when the area is clicked
    uploadArea.addEventListener('click', (e) => {
        let uploadInput = document.getElementById('upload-input')
        uploadInput.click()

    })
    // Prevent the browser from opening the files
    document.addEventListener('dragover', (e) => e.preventDefault())
    document.addEventListener('drop', (e) => e.preventDefault())

    return true;
}

const uploadInput = () => {
    let uploadInput = document.getElementById('upload-input')
    uploadInput.addEventListener('click', (e) => {
        console.log("uploadInput was clicked")
        // Disable default behavior
    })

    // Grab the files uploaded
    uploadInput.addEventListener('change', (e) => {
        console.log("new files were uploaded")
        let files = e.target.files
        handleUpload(files)
        // Disable default behavior
    })
}


const handleUpload = (files) => {
    for (let file of files) {
        // Only deal with png
        if (file.type in supportedFormats)
            console.log(`name: ${file.name}`)
    }
}


export {
    uploadArea,
    uploadInput
};