/**
 * Create a file uploader
 * @link https://pqina.nl/filepond/docs/patterns/api/server/
 */

const uploadURI = './upload' // backend endpoint
const getSupportedFormats = () => ['image/svg']
const pond = FilePond.create(
    document.querySelector('input.filepond'), {
        acceptedFileTypes: ['image/svg'],
        labelFileProcessingComplete: 'File ready'
    }
);

// global variables
let blocks = document.querySelectorAll(".steps-blocks .step")
let blockIndicators = Array.from(document.querySelectorAll(".steps-pagination a"))
let blockButtonControllers = document.querySelectorAll(".steps-buttons button")
let nextButton = document.getElementById('next-button')
let notifyWrapper = document.querySelector('.steps-blocks .notify')
let detailsWrapper = document.querySelector('.steps-blocks .details')
let confirmWrapper = document.querySelector('.steps-blocks .confirm')
let confirmButton = document.getElementById("next-button")
let returnButton = document.getElementById("prev-button")

let position = 1
let confirmed = false;
let icons = {}

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
    for (let icon of Object.values(icons)) {

        let formData = new FormData();
        formData.append("icon", JSON.stringify({
            "author": icon.getAuthor(),
            "content": icon.getCleanVersion(),
            "filename": icon.getFileName(),
            "title": icon.getTitle(),
            "wikicode": icon.getWikiCode()
        }))

        // display standby animation
        nextButton.classList.add('standby')
        confirmWrapper.display = 'none'

        fetch(uploadURI, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {

                nextButton.classList.remove('standby')

                // hide other steps
                blocks.forEach(e => {
                    e.classList.remove("active")
                })

                notifyWrapper.classList.add('active')

                if ('icon' in data) {

                    // show notification step
                    notifyWrapper.querySelector(".card-title").innerHTML = 'Congratulations!'

                    // add the permalink the notification area
                    data.message = `<a href="${data.icon.path}">${data.icon.title}</a> was uploaded`
                    displayApiMessage(data);

                } else {
                    notifyWrapper.classList.add('active')
                    displayApiMessage(data);
                }

                // hide controls
                nextButton.style.display = 'none'
                returnButton.style.display = 'none'
                document.querySelector('.steps-pagination').style.display = 'none'

            }).catch(e => {
                console.log(e)
                let data = {
                    message: `Error with ${icon.getTitle()}. Please try again.`
                };

                displayApiMessage(data);
            })
    }
}

const displayApiMessage = (data) => {

    // Display errors
    let liNode = document.createElement("li")
    liNode.innerHTML = data.message
    if (!('icon' in data))
        liNode.classList.add("error")
    notifyWrapper.querySelector("ul").appendChild(liNode)

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
            if (!(icon.getSlug() in icons)) {
                detailsWrapper.appendChild(detailsForm)

                // add current icon to array
                icons[icon.getSlug()] = icon

                // Refresh summary by default
                generateSummary()

                // add event listenler
                document.querySelectorAll(`#${icon.getSlug()} input`).forEach(input => {
                    input.addEventListener('keyup', e => {
                        icon.title = document.querySelector(`#${icon.getSlug()} input[name=title]`).value
                        icon.author = document.querySelector(`#${icon.getSlug()} input[name=author]`).value

                        // Refresh summary page
                        generateSummary()
                    })
                })

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
        //console.log("position", cardPosition)
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

    // set event listeners on PrevButton and nextButton
    blockButtonControllers.forEach(controller => {
        controller.addEventListener('click', (e) => {
            // check whether button is "Next" or "Prev"
            let type = (controller.id === "prev-button") ? "prev" : "next"

            if (type === "next") {
                position += 1
            } else {
                position -= 1
            }
            //console.log(position)

            // set position min and max
            if (position >= 3) {
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

            // show buttons once we reach 2nd step
            if (position >= 1) {
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
            if (position <= 3) { // step 4 is handled separately below
                blockIndicators[position - 1].classList.add("active")
                blocks[position - 1].classList.add("active")
            }

            // upload to server on step 3
            if (type === "next" && position >= 4 /*&& !confirmed*/ ) {
                position = blocks.length - 1
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
    <div class="card-body" id="${icon.getSlug()}">
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
 * Generate summary tab which contains
 * a list of all icons
 */
const generateSummary = () => {
    confirmWrapper.querySelector("ul").innerHTML = ""

    for (let icon of Object.values(icons)) {
        // clear previous items

        // summarize the file list for confirmation                        
        let liNode = document.createElement("li")
        liNode.innerHTML = `${icon.getTitle()}, by ${icon.getAuthor()}`
        confirmWrapper.querySelector("ul").appendChild(liNode)
    }
}

/**
 * Centralize event listeners for code readability.
 * TODO: consider a better code organization
 */
const initListeners = () => {
    displayMultistepForm()
}

initListeners() // trigger event listeners