<?

include "db.php";

function render_courses($dept, $start, $end) {
    $db = db_connect();
    
    $terms = courses_get_terms($db, $dept);
    $current_term = courses_get_current_term();
    $ranged_terms = courses_filter_terms_default($terms, $current_term);
    $subjects = courses_get_subjects($db, $dept);
    $careers = courses_get_careers($db, $dept);
    $courses = courses_get_courses($db, $dept);
    
    $tolower_terms = array_map('strtolower', $terms);
    if (!empty($start) && !empty(end)) {
        if (in_array($start, $tolower_terms) && in_array($end, $tolower_terms)) {
            $ranged_terms = courses_filter_terms($terms, $start, $end);
        } else {
            echo '<div class="alert alert-danger">';
            echo '<p>Please ensure the start and end terms are correct (in spelling and order) and from the list below:</p>';
            echo '<ul class="mb-0">';
            foreach($terms as $term) {
                echo '<li>' . $term . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            return;
        }
    }

    echo '<h2 id="termHeader" class="text-primary">' . $current_term . '</h2>';
    courses_render_filters($ranged_terms, $subjects, $careers);
    courses_render_courses($courses);
    
    ?>
        <script>
            let courses = <?= json_encode($courses) ?>;
            let initialCurrentTerm = "<?= $current_term ?>";
                
            let termHeader = document.getElementById("termHeader");
            let noDataRow = document.getElementById("noDataRow");
                
            let termFilter = document.getElementById("termFilter");
            let subjectFilter = document.getElementById("subjectFilter");
            let careerFilter = document.getElementById("careerFilter");
            let searchFilter = document.getElementById("searchFilter");
    
            let tbody = document.getElementById("coursesBody");
                
            window.onload = resetFilters();
    
            let filteredCourses = courses.filter(intialCourses);
            renderCourses();
        </script> 
    <?

    db_close($db);
}

function courses_get_terms($db, $dept) {
    $sql = "SELECT DISTINCT term FROM courses WHERE department_id = " . $dept . " ORDER BY SUBSTRING_INDEX(term, ' ', -1) DESC, FIELD(SUBSTRING_INDEX(term, ' ', 1), 'Fall', 'Summer', 'Spring')";

    $res = db_query($db, $sql);

    $terms = array();
    
    if ($res) {
        foreach ($res as $term) {
            array_push($terms, $term['term']);
        }
    }

    return $terms;
}

function courses_filter_terms_default($terms, $current_term) {
    $s = sizeof($terms);
    $e = 0;

    foreach($terms as $i=>$term) {
        if (strtolower($term) == strtolower($current_term)) {
            $cti = $i;
            break;
        }
    }

    if ($cti == $s) {
        $r = range($s - 1, $s);
    } else if ($cti == $e) {
        $r = range($e, $e + 1);
    } else {
        $r = range($cti - 1, $cti + 1);
    }

    return array_intersect_key($terms, array_flip($r));
}

function courses_filter_terms($terms, $start, $end) {
    $s = sizeof($terms);
    $e = 0;

    foreach($terms as $i=>$term) {
        if (strtolower($term) == $end) {
            $e = $i;
        }
        
        if (strtolower($term) == $start) {
            $s = $i;
        }
    }

    return array_intersect_key($terms, array_flip(range($e, $s)));
}

function courses_get_current_term() {
    date_default_timezone_set('America/New_York');
    $now = getdate();

    if ($now['mon'] < 6) {
        $term = "Spring " . $now['year'];
    } else if ($now['mon'] > 7) {
        $term = "Fall " . $now['year'];
    } else {
        $term = "Summer " . $now['year'];
    }
    
    return $term;
}

function courses_get_subjects($db, $dept) {
    $sql = "SELECT DISTINCT prefix FROM courses WHERE department_id = " . $dept . " ORDER BY prefix ASC";

    $res = db_query($db, $sql);

    $subjects = array();
    
    if ($res) {
        foreach ($res as $subject) {
            array_push($subjects, $subject['prefix']);
        }
    }

    return $subjects;
}

function courses_get_careers($db, $dept) {
    $sql = "SELECT DISTINCT career FROM courses WHERE department_id = " . $dept . " ORDER BY career ASC";

    $res = db_query($db, $sql);

    $careers = array();
    
    if ($res) {
        foreach ($res as $career) {
            array_push($careers, $career['career']);
        }
    }

    return $careers;
}

function courses_render_filters($terms, $subjects, $careers) {
    $current_term = courses_get_current_term();

    ?>

    <div class="d-flex flex-wrap justify-content-between mt-3 mb-4">
        <div>
            <label for="termFilter" class="mr-2 h6">Term:</label>
            <select name="term" id="termFilter" oninput="handleTermFilter(this.value)">
                <?

                foreach($terms as $term) {
                    ?>

                    <option value="<?= $term ?>"><?= $term ?></option>

                    <?
                }

                ?>
            </select>
        </div>
        
        <div>
            <label for="subjectFilter" class="mr-2 h6">Subject:</label>
            <select name="subject" id="subjectFilter" oninput="handleSubjectFilter(this.value)">
                <option value="ALL">All</option>
                
                <?

                foreach($subjects as $subject) {
                    ?>

                    <option value="<?= $subject ?>"><?= $subject ?></option>

                    <?
                }

                ?>
            </select>
        </div>

        <div>
            <label for="careerFilter" class="mr-2 h6">Career:</label>
            <select name="career" id="careerFilter" oninput="handleCareerFilter(this.value)">
                <option value="ALL">All</option>

                <?

                foreach($careers as $career) {
                    ?>

                    <option value="<?= $career ?>"><?= $career ?></option>

                    <?
                }

                ?>
            </select>
        </div>
        
        <div>
            <label for="searchFilter" class="mr-2 h6">Search:</label>
            <input type="text" id="searchFilter" oninput="handlesSearchFilter()">
        </div>
    </div>

    <?
}

function courses_get_courses($db, $dept) {
    $sql = "SELECT courses.term, CONCAT(courses.prefix, courses.catalog_number) AS course, courses.prefix, courses.section, courses.title, courses.user_id as instructor_id, users.lname, courses.instruction_mode, courses.meeting_days, courses.class_start, courses.class_end, courses.description, courses.syllabus_file, courses.career FROM courses JOIN users ON courses.user_id = users.id WHERE courses.department_id = " . $dept . " ORDER BY SUBSTRING_INDEX(term, ' ', -1) DESC, FIELD(SUBSTRING_INDEX(term, ' ', 1), 'Fall', 'Summer', 'Spring'), course ASC";

    $res = db_query($db, $sql);

    $courses = array();
    
    if ($res) {
        foreach ($res as $course) {
            array_push($courses, array(
                "term" => $course["term"],
                "course" => $course["course"],
                "course_prefix" => $course["prefix"],
                "title" => $course["title"],
                "instructor_link" => courses_format_instructor_link($course["instructor_id"]),
                "instructor_lname" => $course["lname"],
                "mode_long" => courses_format_mode($course["instruction_mode"], "long"),
                "mode_short" => courses_format_mode($course["instruction_mode"], "short"),
                "meeting_days" => $course["meeting_days"],
                "meeting_times" => courses_format_meeting_times($course["class_start"], $course["class_end"]),
                "description" => $course["description"],
                "syllabus" => courses_format_syllabus($course["syllabus_file"], $course["course"], $course["section"], $course["term"]),
                "career" => $course["career"],
            ));
        }
    }

    return $courses;
}

function courses_format_instructor_link($id) {
    if (!empty($id)) {
        return "/faculty-staff?id=" . $id;
    } else {
        return "";
    }
}

function courses_format_mode($mode, $type) {
    if (!empty($mode)) {
        if ($type == "long") {
            return preg_replace('/\s*\((.*?)\)/', '', $mode);
        }

        if ($type == "short") {
            preg_match('/\((.*?)\)/', $mode, $match);
            return $match[1];
        }
    } else {
        return "";
    }
}

function courses_format_meeting_times($start, $end) {
    if (!empty($start)) {
        $s = date_format(date_create($start), "g:i a");
        $e = date_format(date_create($end), "g:i a");

        return $s . " – " . $e;
    } else {
        return "";
    }
}

function courses_format_syllabus($syllabus, $course, $section, $term) {
    if ($syllabus == 1) {
        return "http://www.cah.ucf.edu/common/files/syllabi/" . $course . $section . str_replace(' ', '', $term) . ".pdf";
    } else {
        return "";
    }
}

function courses_render_courses($courses) {
    ?>

    <div class="modal" id="descModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="descModalTitle" class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div id="descModalBody" class="modal-body"></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="cursor: pointer">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <colgroup>
                <col>
                <col>
                <col>
                <col style="width: 5%;">
                <col>
                <col style="width: 10%;">
                <col style="width: 10%;">
                <col style="width: 10%;">
            </colgroup>

            <thead>
                <tr>
                    <th class="text-center p-3">Course</th>
                    <th class="text-center p-3">Title</th>
                    <th class="text-center p-3">Instructor</th>
                    <th class="text-center p-3">Mode</th>
                    <th class="text-center p-3">Day(s)/Times</th>
                    <th class="text-center p-3">Description</th>
                    <th class="text-center p-3">Syllabus</th>
                    <th class="text-center p-3">Career</th>
                </tr>
            </thead>
            <tbody id="coursesBody">
                <tr id="noDataRow">
                    <td class="text-center p-3" colspan="8"><em>No data found.</em></td>
                </tr>
            </tbody>
        </table>
    </div>

    <?
}

?>