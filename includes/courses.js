function resetFilters() {
    Object.keys(termFilter).forEach(function(term) {
        let i = term;
        let t = termFilter[term];
        
        if (t.innerHTML.trim() == initialCurrentTerm) {
            termFilter.selectedIndex = i;
        }
    });

    subjectFilter.selectedIndex = 0;
    careerFilter.selectedIndex = 0;
    searchFilter.value = "";
}

function handleTermFilter(value) {
    termFilter.value = value;
    termHeader.innerHTML = value;

    handleFilters();
}

function handleSubjectFilter(value) {
    subjectFilter.value = value;

    handleFilters();
}

function handleCareerFilter(value) {
    careerFilter.value = value;

    handleFilters();
}

function handlesSearchFilter() {
    handleFilters();
}

function handleFilters() {
    tbody.innerHTML = '';
    tbody.appendChild(noDataRow);

    filteredCourses = courses.filter(function(course) {
        return course["term"] == termFilter.value;
    });
    
    if (subjectFilter.value != "ALL") {
        filteredCourses = filteredCourses.filter(function(course) {
            return course["course_prefix"] == subjectFilter.value;
        });
    }

    if (careerFilter.value != "ALL") {
        filteredCourses = filteredCourses.filter(function(course) {
            return course["career"] == careerFilter.value;
        });
    }

    if (searchFilter.value) {
        filteredCourses = filteredCourses.filter(function(course) {
            let s = searchFilter.value.toLowerCase();
            let c = course["course"].toLowerCase();
            let t = course["title"].toLowerCase();
            let i = course["instructor_lname"].toLowerCase();

            return c.includes(s) || t.includes(s) || i.includes(s);
        });
    }
    
    renderCourses();
}

function intialCourses(course) {
    return course["term"] == initialCurrentTerm;
}

function renderCourses() {
    if (filteredCourses.length > 0) {
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()

            $(".descModalBtn").click(function() {
                $('#descModal').modal('show')
                $('.modal-backdrop').addClass('fade')
                $('.modal-backdrop').addClass('show')
            })
        })

        noDataRow.style.display = "none";

        let dataToRender = 8;
    
        Object.keys(filteredCourses).forEach(function(course) {
            let r = course;
            let c = filteredCourses[course];
    
            let row = document.createElement("TR");
    
            for (let i = 0; i < dataToRender; i++) {
                let td = document.createElement("TD");
                td.classList.add("text-center");
                td.classList.add("align-middle");
                td.style.fontSize = "0.875rem";
    
                switch(i) {
                    case 0:
                        // Course
                        td.innerHTML = c["course"];
                        break;
                    case 1:
                        // Title
                        td.innerHTML = "<b>" + c["title"] + "</b>";
                        break;
                    case 2:
                        // Instructor
                        td.innerHTML = '<a href="' + c["instructor_link"] + '">' + c["instructor_lname"] + "</a>";
                        break;
                    case 3:
                        // Mode
                        td.style.textDecoration = "underline";
                        td.style.cursor = "pointer";
                        td.setAttribute("data-toggle", "tooltip");
                        td.setAttribute("data-placement", "bottom");
                        td.title = c["mode_long"];

                        td.innerHTML = c["mode_short"] ;
                        break;
                    case 4:
                        // Day(s)/Times
                        td.innerHTML = c["meeting_days"] + '<br><span class="small">' + c["meeting_times"] + '</span>';
                        break;
                    case 5:
                        // Description
                        let d = c["description"];
                        
                        if (d) {
                            td.style.cursor = "pointer";
                            td.setAttribute("data-toggle", "tooltip");
                            td.setAttribute("data-placement", "bottom");
                            td.title = "Click for course description";
                            td.classList.add("descModalBtn");
                            td.onclick = function() {
                                let modalTitle = document.getElementById("descModalTitle");
                                modalTitle.innerHTML = c["course"] + " &mdash; " + c["title"];

                                let modalDesc = document.getElementById("descModalBody");
                                modalDesc.innerHTML = d;
                            };
                            
                            td.innerHTML = "<svg style='width: 25%' xmlns='http://www.w3.org/2000/svg' class='ionicon' viewBox='0 0 512 512'><circle cx='256' cy='256' r='48'/><circle cx='416' cy='256' r='48'/><circle cx='96' cy='256' r='48'/></svg>";
                        } else {
                            td.classList.add("text-muted");
                            td.style.fontSize = "smaller";

                            td.innerHTML = "N/A";
                        }
                        break;
                    case 6:
                        // Syllabus
                        let s = c["syllabus"];
    
                        if (s) {
                            td.style.cursor = "pointer";
                            td.setAttribute("data-toggle", "tooltip");
                            td.setAttribute("data-placement", "bottom");
                            td.title = "Download the syllabus as a PDF";

                            td.innerHTML = "<a href='" + c["syllabus"] + "'><svg style='width: 25%' xmlns='http://www.w3.org/2000/svg' class='ionicon' viewBox='0 0 512 512'><path d='M416 221.25V416a48 48 0 01-48 48H144a48 48 0 01-48-48V96a48 48 0 0148-48h98.75a32 32 0 0122.62 9.37l141.26 141.26a32 32 0 019.37 22.62z' fill='none' stroke='currentColor' stroke-linejoin='round' stroke-width='32'/><path d='M256 56v120a32 32 0 0032 32h120' fill='none' stroke='currentColor' stroke-linecap='round' stroke-linejoin='round' stroke-width='32'/></svg></a>";
                        } else {
                            td.classList.add("text-muted");
                            td.style.fontSize = "smaller";

                            td.innerHTML = "N/A";
                        }
                        break;
                    case 7:
                        // Career
                        td.innerHTML = c["career"];
                        break;
                }
    
                row.appendChild(td);
            }
    
            tbody.appendChild(row);
        });
    } else {
        noDataRow.style.display = "";
    }
}