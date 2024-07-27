function persnalization() {
    const personlizationElement = document.querySelectorAll('.personalization');

    console.log(personlizationElement.length);
    if (personlizationElement.length > 0) {
        personlizationElement.forEach(element => {
            if (element) {
                element.addEventListener('click', function(currentElement) {
                    const classNameToCheck = 'hide-element';

                    const elementToShow = currentElement.target.getAttribute('data-show');
                    const tableToShow = document.querySelector('#' + elementToShow);
                    if (tableToShow.classList.contains(classNameToCheck)) {
                        tableToShow.classList.remove(classNameToCheck);
                        element.querySelector("i.arrow.personlazation-hide.up").style.display = "inline-block";
                        element.querySelector("i.arrow.down").style.display = "none";
                       


                    } else {
                        tableToShow.classList.add(classNameToCheck);
                        element.querySelector("i.arrow.down").style.display = "inline-block";
                        element.querySelector("i.arrow.personlazation-hide.up").style.display = "none";

                    }
                })
            }
        })
    }
}


setTimeout(persnalization, 5000);
window.addEventListener("load", function() {
    //document.querySelector("i.arrow.personlazation-hide.up").style.display = "none";
    //document.querySelector("i.arrow.personlazation-hide.up").css("display", "none");
    //document.querySelector(".totals-tax th").setAttribute("colspan", "3");
});