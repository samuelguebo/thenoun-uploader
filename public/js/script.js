/**
 * Create a file uploader
 * @link https://pqina.nl/filepond/docs/patterns/api/server/
 */

const uploadURI = './upload' // backend endpoint
const pond = FilePond.create(
    document.querySelector('input.filepond'), {
        acceptedFileTypes: ['image/svg'],
        maxFileSize: '500KB',
        labelFileProcessingComplete: 'File ready'
    }
);
// override Pond upload
pond.setOptions({
    server: {
        url: uploadURI,
        // disable asynchronous upload, just return the file
        process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
            //  update the progress to 100% 
            load(200); // ideally, do some logic
        },
    }
});

/**
 * Improve UX by hiding or dislaying
 * the upload button
 */
const displayUploadButton = () => {
    let uploadButton = document.getElementById("upload-button")
    // deal with each file
    pond.on('addfile', (error, file) => {
        uploadButton.style.display = 'block';
    });

    pond.on('removefile', (error, file) => {
        let files = pond.getFiles()
        // Hide upload button
        if (files.length < 1)
            uploadButton.style.display = 'none';
    });

    uploadButton.addEventListener('click', (e) => {
        e.preventDefault();
        uploadToServer(); // push to backend
    })
}

/**
 * Override Pond upload with a custom the logic
 * for sending icons to server for further 
 * processing (i.e: send to wiki, etc.)
 */
const uploadToServer = () => {
    //pond.processFiles().then(files => {
    let files = pond.getFiles()
    // done processing files
    let formData = new FormData();

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        formData.append(file.file.name, file.file)

    }
    // post data
    fetch(uploadURI, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => console.log(data))
    //})
}

/**
 * Centralize event listeners for code readability.
 * Consider a better approach to code organization
 */
const initListeners = () => {
    displayUploadButton()
}

initListeners() // trigger event listeners