/**
 * 
 */
const getSupportedFormats = () => {
    return ['image/png', 'image/svg', '']
}

const preventDropConflict = () => {
    // Prevent the browser from opening the files
    document.addEventListener('dragover', (e) => e.preventDefault())
    document.addEventListener('drop', (e) => e.preventDefault())
}

/**
 * 
 */
const getUploadArea = () => {
    return document.getElementById("upload-area");
}
const uploadAreaAction = () => {

    // Trigger the drop event 
    getUploadArea().addEventListener('drop', (e) => {
        console.log('entered onDrop')

        let data = e.dataTransfer;
        let files = data.files ? data.files : data.items
        handleUpload(files)

        // Disable default behavior
        e.preventDefault();

    })

    // Trigger inputUpload when the area is clicked
    getUploadArea().addEventListener('click', (e) => {
        getUploadInput().click()
    })

    return true;
}
/**
 * 
 */
const getUploadInput = () => {
    return document.getElementById('upload-input')
}

const uploadInputAction = () => {
    getUploadInput().addEventListener('click', (e) => {
        console.log("uploadInput was clicked")
        // Disable default behavior
    })

    // Grab the files uploaded
    getUploadInput().addEventListener('change', (e) => {
        console.log("new files were uploaded")
        let files = e.target.files
        handleUpload(files)
        // Disable default behavior
    })
}

/**
 * Binding action to upload UI accordingly
 * @param {*} files 
 */
const handleUpload = (files) => {
    for (let file of files) {
        // Only deal with png
        if (getSupportedFormats().indexOf(file.type) > -1)
            console.log(`name: ${file.name}`)
    }
}


export {
    uploadAreaAction as uploadArea,
    uploadInputAction as uploadInput,
    preventDropConflict
};