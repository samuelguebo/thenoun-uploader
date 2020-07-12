/**
 * Create a file uploader
 */

const pond = FilePond.create(
    document.querySelector('input.filepond')
);

/**
 * Hide and show upload button
 */
const displayUploadButton = () => {
    let uploadButton = document.getElementById("upload-button")
    // deal with each file
    pond.on('addfile', (error, file) => {
        if (error) {
            console.log('Oh no');
            return;
        }
        //console.log('File added', file);
        uploadButton.style.display = 'block';
    });

    pond.on('removefile', (error, file) => {
        let files = pond.getFiles()

        // Hide upload button
        if (files.length < 1) {
            uploadButton.style.display = 'none';
        }
        //console.log('File added', file);

    });
}

displayUploadButton()