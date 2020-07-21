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
 * Manage the appearing and dispearing of 
 * each part of the multistep form
 */

const displayMultistepForm = () => {
    let blocks = document.querySelectorAll(".steps-blocks .step")
    let blockIndicators = document.querySelectorAll(".steps-pagination a")
    let position = 0 // 0 is block 1
    let blockButtonControllers = document.querySelectorAll(".steps-buttons button")
    blockIndicators = Array.from(blockIndicators)

    blockButtonControllers.forEach(controller => {
        controller.addEventListener('click', (e) => {

            // check whether button is "Next" or "Prev"
            let type = (controller.id === "prev-button") ? "prev" : "next"

            if (type === "next") {
                position += 1
            } else {
                position -= 1
            }

            // set position min and max
            if (position >= blocks.length - 1) {
                position = blocks.length - 1
                // hide button-prev
                document.getElementById("next-button").style.display = "none"
            }

            if (position <= 0) {
                position = 0
                // hide button-next
                document.getElementById("prev-button").style.display = "none"
            }

            // show buttons accordingly
            if (position == 1) {
                document.getElementById("prev-button").style.display = "inline-block"
                document.getElementById("next-button").style.display = "inline-block"
            }

            // remove "active" from indicators
            blockIndicators.forEach(e => {
                e.classList.remove("active")
            })

            // do the same for blocks
            blocks.forEach(e => {
                e.classList.remove("active")
            })

            // activate the current controller and block 
            blockIndicators[position].classList.add("active")
            blocks[position].classList.add("active")
        })
    })

}

/**
 * Generate forms with details for each
 * icons that was uploaded
 */
const handleIconDescriptions = () => {
    let nextButton = document.getElementById('next-button')
    let detailsWrapper = document.querySelector('.steps-blocks .details')

    // update DOM automatically
    nextButton.addEventListener('click', e => {
        let files = pond.getFiles()

        files.forEach(file => {
            file = file.file // restructure object
            // file content can only be read asynchronously
            const reader = new FileReader();
            reader.onload = (f) => {
                f["name"] = file.name
                let icon = new Icon(f)
                let detailsForm = document.createElement('div')
                detailsForm.classList.add("card")
                detailsForm.innerHTML = formDetailTemplate(icon)
                detailsWrapper.appendChild(detailsForm)
            }

            reader.readAsText(file)
        })
    })
}

const formDetailTemplate = (icon) => {
    return `
    <div class="card-body">
        <h5 class="card-title">${icon.getFileName()}</h5>
        <form>
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" placeholder="Enter title" name="title" value="${icon.getTitle()}">
        </div>
        <form>
        <div class="form-group">
            <label for="author">Author</label>
            <input type="text" class="form-control" placeholder="Specify the author" name="author" value="${icon.getAuthor()}">
        </div>
        </form>
    </div>`
}
/**
 * Centralize event listeners for code readability.
 * Consider a better approach to code organization
 */
const initListeners = () => {
    displayUploadButton()
    displayMultistepForm()
    handleIconDescriptions()
}

initListeners() // trigger event listeners