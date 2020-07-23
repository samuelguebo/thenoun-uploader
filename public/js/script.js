/**
 * Create a file uploader
 * @link https://pqina.nl/filepond/docs/patterns/api/server/
 */

const uploadURI = './upload' // backend endpoint
const getSupportedFormats = () => ['image/svg']
const pond = FilePond.create(
    document.querySelector('input.filepond'), {
        acceptedFileTypes: ['image/svg'],
        maxFileSize: '500KB',
        labelFileProcessingComplete: 'File ready'
    }
);

// global variables
let blocks = document.querySelectorAll(".steps-blocks .step")
let blockIndicators = Array.from(document.querySelectorAll(".steps-pagination a"))
let blockButtonControllers = document.querySelectorAll(".steps-buttons button")
let nextButton = document.getElementById('next-button')
let detailsWrapper = document.querySelector('.steps-blocks .details')
let confirmWrapper = document.querySelector('.steps-blocks .confirm')
let confirmButton = document.getElementById("next-button")
let returnButton = document.getElementById("prev-button")

let position = 0 // 0 is block 1
let confirmCounter = 0;
let confirmed = false;
let detailsIds = []
let icons = []

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
 * Override Pond upload with a custom the logic
 * for sending icons to server for further 
 * processing (i.e: send to wiki, etc.)
 */
const uploadToServer = () => {
    // Upload each file a parralel process (asynchronously)
    for (let i = 0; i < icons.length; i++) {

        const icon = icons[i];
        let formData = new FormData();
        formData.append(icon.getFileName(), icon.getFile())
        formData.append("icon", JSON.stringify({
            "title": icon.getTitle(),
            "author": icon.getAuthor(),
            "wikicode": icon.getWikiCode()
        }))

        fetch(uploadURI, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => console.log(data))
    }
}

/**
 * Manage the appearing and dispearing of 
 * each part of the multistep form
 */
const displayMultistepForm = () => {
    // show nextButton when a file is added
    pond.on('addfile', (error, file) => {
        confirmButton.style.display = 'block';

        file = file.file // restructure object
        // file content can only be read asynchronously
        const reader = new FileReader();
        let icon = new Icon(file)
        reader.onload = (f) => {
            f["name"] = icon.getFile().name
            icon.content = f.target.result
            let detailsForm = document.createElement('div')
            detailsForm.classList.add("card")
            detailsForm.innerHTML = formDetailTemplate(icon)

            // add form details but avoid duplication
            if (detailsIds.indexOf(icon.getFile().name) < 0) {
                detailsWrapper.appendChild(detailsForm)
                detailsIds.push(icon.getFile().name)
                icons.push(icon)

                // summarize the file list for confirmation                        
                let liNode = document.createElement("li")
                liNode.innerHTML = `${icon.getTitle()}, by ${icon.getAuthor()}`
                confirmWrapper.querySelector("ul").appendChild(liNode)
            }

        }

        reader.readAsText(file)
    });

    // remove relevant card from details block when an icon is removed
    pond.beforeRemoveFile = (file => {
        // disable this feature until we reach step 2

        // otherwise, proceed
        let cardPosition = null
        let files = pond.getFiles()
        files.forEach(f => {
            if (f.id === file.id) {
                cardPosition = files.indexOf(f)
                detailsIds.pop(file.file.name)
            }
        })
        console.log("position", cardPosition)
        detailsWrapper.querySelector(`div:nth-child(${cardPosition+1})`).remove()
        return true

    })
    // hide nextButton when there are no files
    pond.on('removefile', (error, file) => {
        let files = pond.getFiles()
        // Hide upload button
        if (files.length < 1)
            confirmButton.style.display = 'none';

    });


    blockButtonControllers.forEach(controller => {
        controller.addEventListener('click', (e) => {

            // check whether button is "Next" or "Prev"
            let type = (controller.id === "prev-button") ? "prev" : "next"

            if (type === "next") {
                position += 1
            } else {
                position -= 1
            }
            confirmCounter = position

            // set position min and max
            if (position >= blocks.length - 1) {
                position = blocks.length - 1

                // hide button-prev
                confirmButton.innerHTML = 'Confirm <i class="fa fa fa-check"></i> '
            } else {
                confirmButton.innerHTML = 'Next <i class="fa fa fa-angle-right"></i>'
            }

            if (position <= 0) {
                position = 0
                // hide button-next
                returnButton.style.display = "none"
            }

            // show buttons accordingly
            if (position == 1) {
                returnButton.style.display = "inline-block"
                confirmButton.style.display = "inline-block"
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

            if (type === "next" && confirmCounter == blocks.length /*&& !confirmed*/ ) {
                confirmed = true; // reset
                uploadToServer(); // push to backend
            }

        })
    })

}

/**
 * Boilerplate that uses details from an Icon object
 * and generates HTML code
 * 
 * @param {Icon object} icon 
 * @returns String HTML code
 */
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
 * TODO: consider a better code organization
 */
const initListeners = () => {
    displayMultistepForm()
}

initListeners() // trigger event listeners