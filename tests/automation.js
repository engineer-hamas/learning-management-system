const puppeteer = require("puppeteer");
const fs = require("fs");
const path = require("path");

const BASE_URL = "http://localhost:3000";
const SCREENSHOT_DIR = path.join(__dirname, "design");

// Ensure screenshot directory exists
if (!fs.existsSync(SCREENSHOT_DIR)) {
  fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

let screenshotCounter = 1;
const dummyFilePath = path.join(__dirname, "dummy.pdf");
if (!fs.existsSync(dummyFilePath)) {
  fs.writeFileSync(dummyFilePath, "dummy pdf content");
}

async function takeScreenshot(page, name) {
  const filename = `${String(screenshotCounter).padStart(2, "0")}_${name}.png`;
  const filepath = path.join(SCREENSHOT_DIR, filename);
  await page.screenshot({ path: filepath, fullPage: true });
  console.log(`📸 Screenshot saved: ${filename}`);
  screenshotCounter++;
}

async function delay(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

(async () => {
  console.log("🚀 Starting automation test suite...");
  const browser = await puppeteer.launch({
    headless: "new",
    args: ["--no-sandbox"],
  });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 800 });

  try {
    // --- 1. ADMIN FLOW ---
    console.log("🔑 Logging in as Admin...");
    await page.goto(`${BASE_URL}/login`);
    await takeScreenshot(page, "login_view");
    await Promise.all([
      page.waitForNavigation({ waitUntil: "domcontentloaded" }),
      page.evaluate(() => {
        document.querySelector('input[name="email"]').value = "admin@example.com";
        document.querySelector('input[name="password"]').value = "password";
        document.querySelector('form:not([action="/logout"])').submit();
      }),
    ]);
    await takeScreenshot(page, "admin_dashboard");

    // Create Teacher
    console.log("👤 Creating Teacher...");
    await page.goto(`${BASE_URL}/admin/users/create`);
//    await takeScreenshot(page, "admin_create_teacher_form");
    await page.type('input[name="name"]', "Teacher Three");
    await page.type('input[name="email"]', "teacher3@example.com");
    await page.type('input[name="password"]', "password");
    await page.select('select[name="role"]', "teacher");
    await Promise.all([
      page.waitForNavigation({ waitUntil: "domcontentloaded" }),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);
    await page.waitForSelector(".alert", { timeout: 5000 });
//    await takeScreenshot(page, "admin_create_teacher_success");

    // Create Student
    console.log("👤 Creating Student...");
//    await takeScreenshot(page, "admin_create_student_form");
    await page.type('input[name="name"]', "Student Four");
    await page.type('input[name="email"]', "student4@example.com");
    await page.type('input[name="password"]', "student123");
    await page.select('select[name="role"]', "student");
    await Promise.all([
      page.waitForNavigation({ waitUntil: "domcontentloaded" }),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);
    await page.waitForSelector(".alert", { timeout: 5000 });
//    await takeScreenshot(page, "admin_create_student_success");

    // Create Course
    console.log("📚 Creating Course...");
    await page.goto(`${BASE_URL}/admin/courses/create`);
    // We assume department dropdown has options. We'll pick the first available or try to type.
    // Wait for select[name="department_id"]
    const deptOptions = await page.$$eval(
      'select[name="department_id"] option',
      (options) => options.map((o) => o.value).filter((v) => v)
    );
    if (deptOptions.length > 0) {
      await page.select('select[name="department_id"]', deptOptions[0]);
    }
//    await takeScreenshot(page, "admin_create_course_form");
    await page.type('input[name="name"]', "Artificial Intelligence");
    await page.type('input[name="code"]', "CS501");
    await page.evaluate(() => {
      document.querySelector('input[name="credits"]').value = "";
    });
    await page.type('input[name="credits"]', "3");
    await page.type(
      'textarea[name="description"]',
      "Introduction to AI concepts and algorithms."
    );
    await Promise.all([
      page.waitForNavigation({ waitUntil: "domcontentloaded" }),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);
    await page.waitForSelector(".alert", { timeout: 5000 }).catch(() => {});
//    await takeScreenshot(page, "admin_create_course_success");

    // --- NEW FEATURES: List, Edit, Delete ---

    // List Users
    console.log("👥 Listing Users...");
    await page.goto(`${BASE_URL}/admin/users`);
    await delay(500);
    await takeScreenshot(page, "admin_list_users");

    // Edit User
    console.log("✏️ Editing User...");
    const userEditLinks = await page.$$eval("a", (anchors) =>
      anchors
        .map((a) => a.href)
        .filter((href) => href.includes("/admin/users/edit?id="))
    );
    if (userEditLinks.length > 0) {
      await page.goto(userEditLinks[0]); // Edit the last created user
      await delay(500);
//      await takeScreenshot(page, "admin_edit_user_form");
      await page.type('input[name="name"]', " (Edited)");
      await Promise.all([
        page.waitForNavigation({ waitUntil: "domcontentloaded" }),
        page.evaluate(() =>
          document.querySelector('form:not([action="/logout"])').submit()
        ),
      ]);
//      await takeScreenshot(page, "admin_edit_user_success");
    }

    // List Courses
    console.log("📚 Listing Courses...");
    await page.goto(`${BASE_URL}/admin/courses`);
    await delay(500);
    await takeScreenshot(page, "admin_list_courses");

    // Edit Course
    console.log("✏️ Editing Course...");
    const courseEditLinks = await page.$$eval("a", (anchors) =>
      anchors
        .map((a) => a.href)
        .filter((href) => href.includes("/admin/courses/edit?id="))
    );
    if (courseEditLinks.length > 0) {
      await page.goto(courseEditLinks[courseEditLinks.length - 1]); // Edit the last created course
      await delay(500);
      await page.type('input[name="name"]', " (Edited)");
      await Promise.all([
        page.waitForNavigation({ waitUntil: "domcontentloaded" }),
        page.evaluate(() =>
          document.querySelector('form:not([action="/logout"])').submit()
        ),
      ]);
//      await takeScreenshot(page, "admin_edit_course_success");
    }

    // Setup auto-accept for confirmation dialogs used in deletion
    page.on("dialog", async (dialog) => {
      await dialog.accept();
    });

    // Create a Dummy User to Delete
    console.log("👤 Creating Dummy User for Deletion...");
    await page.goto(`${BASE_URL}/admin/users/create`);
//    await takeScreenshot(page, "admin_create_dummy_user_form");
    await page.type('input[name="name"]', "Student Five");
    await page.type('input[name="email"]', "student5@example.com");
    await page.type('input[name="password"]', "password");
    await page.select('select[name="role"]', "student");
    await Promise.all([
      page.waitForNavigation({ waitUntil: "domcontentloaded" }),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);

    // Delete Dummy User
    console.log("🗑️ Deleting Dummy User...");
    await page.goto(`${BASE_URL}/admin/users`);
    await delay(500);
    const deleteForms = await page.$$('form[action="/admin/users/delete"]');
    if (deleteForms.length > 0) {
      const lastForm = deleteForms[0];
      const deleteBtn = await lastForm.$('button[type="submit"]');
      await Promise.all([
        page.waitForNavigation({ waitUntil: "domcontentloaded" }),
        deleteBtn.click(),
      ]);
//      await takeScreenshot(page, "admin_delete_user_success");
    }

    // Create a Dummy Course to Delete
    console.log("📚 Creating Dummy Course for Deletion...");
    await page.goto(`${BASE_URL}/admin/courses/create`);
    const deptOpts2 = await page.$$eval(
      'select[name="department_id"] option',
      (options) => options.map((o) => o.value).filter((v) => v)
    );
    if (deptOpts2.length > 0) {
      await page.select('select[name="department_id"]', deptOpts2[0]);
    }
//    await takeScreenshot(page, "admin_create_dummy_course_form");
    await page.type('input[name="name"]', "Dummy Delete Course");
    await page.type('input[name="code"]', "DEL-999");
    await page.evaluate(() => {
      document.querySelector('input[name="credits"]').value = "";
    });
    await page.type('input[name="credits"]', "1");
    await Promise.all([
      page.waitForNavigation({ waitUntil: "domcontentloaded" }),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);

    // Delete Dummy Course
    console.log("🗑️ Deleting Dummy Course...");
    await page.goto(`${BASE_URL}/admin/courses`);
    await delay(500);
    const delCourseForms = await page.$$(
      'form[action="/admin/courses/delete"]'
    );
    if (delCourseForms.length > 0) {
      const lastCourseForm = delCourseForms[delCourseForms.length - 1];
      const delBtn = await lastCourseForm.$('button[type="submit"]');
      await Promise.all([
        page.waitForNavigation({ waitUntil: "domcontentloaded" }),
        delBtn.click(),
      ]);
//      await takeScreenshot(page, "admin_delete_course_success");
    }

    // Logout Admin
    console.log("🔓 Logging out Admin...");
    await page.goto(`${BASE_URL}/logout`);

    // --- 2. TEACHER FLOW ---
    console.log("🔑 Logging in as Teacher...");
    await delay(500); // ensure page is fully ready
//    await takeScreenshot(page, "teacher_login_form");
    await Promise.all([
      page.waitForNavigation({ waitUntil: "domcontentloaded" }),
      page.evaluate(() => {
        document.querySelector('input[name="email"]').value = "teacher1@example.com";
        document.querySelector('input[name="password"]').value = "password";
        document.querySelector('form:not([action="/logout"])').submit();
      }),
    ]);
    await takeScreenshot(page, "teacher_dashboard");

    // Teacher Settings (Assign Course)
    console.log("⚙️ Assigning Course to Teacher...");
    await page.goto(`${BASE_URL}/settings`);
//    await takeScreenshot(page, "teacher_settings_view");

    await page.evaluate(() => {
      const hiddenContainer = document.getElementById(
        "teacher-hidden-container"
      );
      if (hiddenContainer) {
        hiddenContainer.innerHTML =
          '<input type="checkbox" name="subjects[]" value="1" checked>';
        hiddenContainer.closest("form").submit();
      }
    });
    await page.waitForNavigation({ waitUntil: "domcontentloaded" });
//    await takeScreenshot(page, "teacher_course_assigned");

    // Go to courses
    console.log("📚 Accessing Course as Teacher...");
    await page.goto(`${BASE_URL}/dashboard`);

    // Usually, a teacher can see courses on the dashboard or we can just access /course?id=1 since it's the first.
    // Let's try to grab the first course link.
    const courseLinks = await page.$$eval("a", (anchors) =>
      anchors.map((a) => a.href).filter((href) => href.includes("/course?id="))
    );
    if (courseLinks.length > 0) {
      await page.goto(courseLinks[0]);
    } else {
      await page.goto(`${BASE_URL}/course?id=1`); // Fallback
    }
    await delay(500);
//    await takeScreenshot(page, "teacher_course_view");

    // removed

    // Add Material
    console.log("➕ Adding Course Material...");
    await page.evaluate(() => openModal("materialModal"));
    await delay(500);
//    await takeScreenshot(page, "teacher_add_material_modal");
    await page.type(
      '#materialModal input[name="title"]',
      "Lecture 1: Intro Slides"
    );
    const fileInputMat = await page.$('#materialModal input[type="file"]');
    await fileInputMat.uploadFile(dummyFilePath);
    await Promise.all([
      page.waitForNavigation({ waitUntil: "domcontentloaded" }),
      page.evaluate(() =>
        document.querySelector("#materialModal form").submit()
      ),
    ]);
//    await takeScreenshot(page, "teacher_material_added");

    // Add Assignment
    console.log("📝 Adding Assignment...");
    await page.evaluate(() => openModal("assignmentModal"));
    await delay(500);
//    await takeScreenshot(page, "teacher_add_assignment_modal");
    await page.type(
      '#assignmentModal input[name="title"]',
      "Homework 1: Fundamentals"
    );
    await page.type(
      '#assignmentModal textarea[name="description"]',
      "Complete the first chapter exercises."
    );
    await page.evaluate(() => {
      document.querySelector('#assignmentModal input[name="due_date"]').value =
        "2026-12-31T23:59";
    });
    await Promise.all([
      page.waitForNavigation({ waitUntil: "domcontentloaded" }),
      page.evaluate(() =>
        document.querySelector("#assignmentModal form").submit()
      ),
    ]);
//    await takeScreenshot(page, "teacher_assignment_added");

    // Add Exam
    console.log("✍️ Scheduling Exam...");
    await page.evaluate(() => openModal("examModal"));
    await delay(500);
//    await takeScreenshot(page, "teacher_add_exam_modal");
    await page.type('#examModal input[name="title"]', "Midterm Exam");
    await page.evaluate(() => {
      document.querySelector('#examModal input[name="exam_date"]').value =
        "2026-12-15T10:00";
    });
    await page.evaluate(() => {
      document.querySelector(
        '#examModal input[name="duration_minutes"]'
      ).value = "";
    });
    await page.type('#examModal input[name="duration_minutes"]', "90");
    await Promise.all([
      page.waitForNavigation({ waitUntil: "domcontentloaded" }),
      page.evaluate(() => document.querySelector("#examModal form").submit()),
    ]);
//    await takeScreenshot(page, "teacher_exam_added");

    // Add Quiz
    console.log("❓ Adding Quiz...");
    await page.evaluate(() => openModal("quizModal"));
    await delay(500);
//    await takeScreenshot(page, "teacher_add_quiz_modal");
    await page.type('#quizModal input[name="title"]', "Pop Quiz 1");
    await page.type(
      '#quizModal textarea[name="description"]',
      "Short 10-question quiz on Chapter 1."
    );
    await Promise.all([
      page.waitForNavigation({ waitUntil: "domcontentloaded" }),
      page.evaluate(() => document.querySelector("#quizModal form").submit()),
    ]);
//    await takeScreenshot(page, "teacher_quiz_added");
    await takeScreenshot(page, "teacher_course_overview_populated");

    // Take Attendance
    console.log("📝 Taking Attendance...");
    const currentUrl = page.url();
    const courseIdMatch = currentUrl.match(/id=(\d+)/);
    const courseId = courseIdMatch ? courseIdMatch[1] : 1;
    await page.goto(`${BASE_URL}/course/attendance?id=${courseId}`);
    await delay(500);
    await takeScreenshot(page, "teacher_attendance_view");

    // Logout Teacher
    console.log("🔓 Logging out Teacher...");
    await page.goto(`${BASE_URL}/logout`);

    // --- 3. STUDENT FLOW ---
    console.log("🔑 Logging in as Student...");
    await delay(500); // ensure page is fully ready
//    await takeScreenshot(page, "student_login_form");
    await Promise.all([
      page.waitForNavigation({ waitUntil: "domcontentloaded" }),
      page.evaluate(() => {
        document.querySelector('input[name="email"]').value = "student1@example.com";
        document.querySelector('input[name="password"]').value = "password";
        document.querySelector('form:not([action="/logout"])').submit();
      }),
    ]);
    await takeScreenshot(page, "student_dashboard");

    // Go to Enrollments
    console.log("🎓 Enrolling in Course...");
    await page.goto(`${BASE_URL}/enrollments`);
    await delay(500);
    await takeScreenshot(page, "student_enrollments_view");

    // Click enroll button if exists
    try {
      if (await page.$('form[action="/enrollments/add"] button')) {
        await Promise.all([
          page.waitForNavigation({ waitUntil: "domcontentloaded" }),
          page.evaluate(() =>
            document
              .querySelector('form[action="/enrollments/add"] button')
              .click()
          ),
        ]);
//        await takeScreenshot(page, "student_enrolled_success");
      }
    } catch (e) {
      console.log("Could not enroll:", e);
    }

    // View Course as Student
    console.log("👀 Viewing Course Content as Student...");
    await page.goto(`${BASE_URL}/dashboard`);
    const stuCourseLinks = await page.$$eval("a", (anchors) =>
      anchors.map((a) => a.href).filter((href) => href.includes("/course?id="))
    );
    if (stuCourseLinks.length > 0) {
      await page.goto(stuCourseLinks[0]);
    } else {
      await page.goto(`${BASE_URL}/course?id=1`); // Fallback
    }
    await delay(500);
    await takeScreenshot(page, "student_course_overview");

    // Expand accordions
    console.log("📂 Expanding Course Sections...");
    await page.evaluate(() => {
      document.querySelectorAll(".section-header").forEach((el) => el.click());
      document
        .querySelectorAll(".accordion-detail")
        .forEach((el) => el.setAttribute("open", "true"));
    });
    await delay(500);
    await takeScreenshot(page, "student_course_content_expanded");

    // Settings / Profile
    console.log("⚙️ Updating Profile...");
    await page.goto(`${BASE_URL}/settings`);
    await delay(500);
//    await takeScreenshot(page, "student_profile_edit_form");
    await page.type(
      'textarea[name="bio"]',
      " I am passionate about learning new technologies and software development."
    );
    await Promise.all([
      page.waitForNavigation({ waitUntil: "domcontentloaded" }),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);
//    await takeScreenshot(page, "student_profile_updated");

    console.log("✅ Automation test suite completed successfully!");
  } catch (error) {
    console.error("❌ Error during automation:", error);
    await takeScreenshot(page, "error_state");
    const html = await page.content();
    fs.writeFileSync(path.join(__dirname, "error_page.html"), html);
    console.log("📄 Saved error page HTML to tests/error_page.html");
  } finally {
    await browser.close();
  }
})();
