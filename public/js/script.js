/**
 * Create a file uploader
 * @link https://pqina.nl/filepond/docs/patterns/api/server/
 */
const uploadURI = './upload' // backend endpoint
const pond = FilePond.create(
    document.querySelector('input.filepond'), {
        acceptedFileTypes: ['image/svg'],
        maxFileSize: '500KB',

    }
);

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
    // override Pond upload
    pond.setOptions({
        server: {
            url: uploadURI,
            // disable asynchronous upload, just return the file
            process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {

                // done processing files
                let formData = new FormData();
                formData.append(fieldName, file, file.name)

                // post data
                fetch(uploadURI, {
                        body: formData,
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log("upload response", data)
                        load(200);
                    }).catch(error => load(300))

            }
        }
    });
}

/**
 * Centralize event listeners for code readability.
 * Consider a better approach to code organization
 */
const initListeners = () => {
    displayUploadButton()
}

initListeners() // trigger event listeners