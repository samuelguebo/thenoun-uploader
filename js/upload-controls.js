/**
 * Binding a series of interactions 
 * to the upload UI accordingly
 *
 * @author Samuel Guebo <@samuelguebo>
 * @version 0.1
 * Copyright 2020
 */

const uploadArea = () => {
    let uploadArea = document.getElementById("upload-area");

    // Trigger the drop event 
    uploadArea.addEventListener('drop', (e)=> {
        console.log('entered onDrop')
        
        let files = {}
        files = e.dataTransfer.files ? e.dataTransfer.files : e.dataTransfer.items
        for (let file of files){
            // Only deal with png
            if(file.type === 'image/png')
                console.log(`name: ${file.name}`)
        }

        // Disable default behavior
        e.preventDefault();

    })

    // Prevent the browser from opening the files
    document.addEventListener('dragover', (e) => e.preventDefault())
    document.addEventListener('drop', (e) => e.preventDefault())

    return true;
}

const uploadInput = () => {
    let uploadInput = document.getElementById('upload-input')
    return uploadInput;
}

export { uploadArea, uploadInput };