const puppeteer = require("puppeteer");
const fs = require("fs");
const path = require("path");

const BASE_URL = "http://localhost:3000";
const SCREENSHOT_DIR = path.join(__dirname, "process");

// Ensure screenshot directory exists
if (!fs.existsSync(SCREENSHOT_DIR)) {
  fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

let screenshotCounter = 1;
const dummyFilePath = path.join(__dirname, "dummy.pdf");
if (!fs.existsSync(dummyFilePath)) {
  fs.writeFileSync(dummyFilePath, "dummy pdf content");
}

async function takeScreenshot(page, name, type) {
  // Only capture if it matches our mode (design vs process)
  if (MODE === "design" && type !== "view") return;
  if (MODE === "process" && type === "view") return;

  const filename = `${String(screenshotCounter).padStart(2, "0")}_${name}.png`;
  const filepath = path.join(SCREENSHOT_DIR, filename);

  // Wait a bit to ensure rendering is complete
  await new Promise((r) => setTimeout(r, 500));

  try {
    await page.screenshot({ path: filepath, fullPage: true });
    console.log(`📸 Screenshot saved: ${filename}`);
    screenshotCounter++;
  } catch (e) {
    console.log(`⚠️ Could not take screenshot ${name}: ${e.message}`);
  }
}

async function delay(time) {
  return new Promise(function (resolve) {
    setTimeout(resolve, time);
  });
}

async function runAutomation() {
  console.log("🚀 Starting automation test suite in " + MODE + " mode...");
  const browser = await puppeteer.launch({
    headless: "new",
    args: ["--no-sandbox"],
  });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 800 });

  // Block BrowserSync to prevent reload race conditions
  await page.setRequestInterception(true);
  page.on("request", (request) => {
    if (request.url().includes("browser-sync")) {
      request.abort();
    } else {
      request.continue();
    }
  });

  // Setup auto-accept for confirmation dialogs used in deletion
  page.on("dialog", async (dialog) => {
    await dialog.accept();
  });

  try {
    // --- 1. ADMIN FLOW ---
    console.log("🔑 Logging in as Admin...");
    await page.goto(`${BASE_URL}/login`);
    await takeScreenshot(page, "login_view", "view");

    await page.type('input[name="email"]', "admin@example.com");
    await page.type('input[name="password"]', "password");
    await takeScreenshot(page, "login_form_filled", "form");

    await Promise.all([
      page.waitForNavigation(),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);
    await takeScreenshot(page, "admin_dashboard", "view");

    // Create Teacher (Validation Test)
    console.log("👤 Creating Teacher...");
    await page.goto(`${BASE_URL}/admin/users/create`);

    // Submit empty to trigger validation
    await Promise.all([
      page.waitForNavigation(),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);
    await takeScreenshot(
      page,
      "admin_create_teacher_validation_error",
      "error"
    );

    await page.type('input[name="name"]', "Teacher NinetyNine");
    await page.type('input[name="email"]', "teacher99@example.com");
    await page.type('input[name="password"]', "password");
    await page.select('select[name="role"]', "teacher");
    await takeScreenshot(page, "admin_create_teacher_form_filled", "form");

    await Promise.all([
      page.waitForNavigation(),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);
    await takeScreenshot(page, "admin_create_teacher_success", "success");

    // Create Student
    console.log("👤 Creating Student...");
    await page.goto(`${BASE_URL}/admin/users/create`);
    await page.type('input[name="name"]', "Student NinetyNine");
    await page.type('input[name="email"]', "student99@example.com");
    await page.type('input[name="password"]', "password");
    await page.select('select[name="role"]', "student");
    await takeScreenshot(page, "admin_create_student_form_filled", "form");

    await Promise.all([
      page.waitForNavigation(),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);
    await takeScreenshot(page, "admin_create_student_success", "success");

    // Create Course
    console.log("📚 Creating Course...");
    await page.goto(`${BASE_URL}/admin/courses/create`);
    await page.type('input[name="name"]', "Introduction to Programming");
    await page.type('input[name="code"]', "CS-101");
    await page.type(
      'textarea[name="description"]',
      "A foundational course covering basic programming concepts."
    );
    const deptOpts = await page.$$eval(
      'select[name="department_id"] option',
      (options) => options.map((o) => o.value).filter((v) => v)
    );
    if (deptOpts.length > 0) {
      await page.select('select[name="department_id"]', deptOpts[0]);
    }
    await takeScreenshot(page, "admin_create_course_form_filled", "form");

    await Promise.all([
      page.waitForNavigation(),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);
    await takeScreenshot(page, "admin_create_course_success", "success");

    // List Users
    console.log("👥 Listing Users...");
    await page.goto(`${BASE_URL}/admin/users`);
    await takeScreenshot(page, "admin_list_users", "view");

    // Edit User
    console.log("✏️ Editing User...");
    const userEditLinks = await page.$$eval("a", (anchors) =>
      anchors
        .map((a) => a.href)
        .filter((href) => href.includes("/admin/users/edit"))
    );
    if (userEditLinks.length > 0) {
      await page.goto(userEditLinks[userEditLinks.length - 1]);
      await page.evaluate(() => {
        document.querySelector('input[name="name"]').value = "Updated User";
      });
      await takeScreenshot(page, "admin_edit_user_form_filled", "form");
      await Promise.all([
        page.waitForNavigation(),
        page.evaluate(() =>
          document.querySelector('form:not([action="/logout"])').submit()
        ),
      ]);
      await takeScreenshot(page, "admin_edit_user_success", "success");
    }

    // List Courses
    console.log("📚 Listing Courses...");
    await page.goto(`${BASE_URL}/admin/courses`);
    await takeScreenshot(page, "admin_list_courses", "view");

    // Edit Course
    console.log("✏️ Editing Course...");
    const courseEditLinks = await page.$$eval("a", (anchors) =>
      anchors
        .map((a) => a.href)
        .filter((href) => href.includes("/admin/courses/edit"))
    );
    if (courseEditLinks.length > 0) {
      await page.goto(courseEditLinks[courseEditLinks.length - 1]);
      await page.evaluate(() => {
        document.querySelector('input[name="name"]').value += " (Updated)";
      });
      await takeScreenshot(page, "admin_edit_course_form_filled", "form");
      await Promise.all([
        page.waitForNavigation(),
        page.evaluate(() =>
          document.querySelector('form:not([action="/logout"])').submit()
        ),
      ]);
      await takeScreenshot(page, "admin_edit_course_success", "success");
    }

    // Create a Dummy User to Delete
    console.log("👤 Creating Dummy User for Deletion...");
    await page.goto(`${BASE_URL}/admin/users/create`);
    await page.type('input[name="name"]', "Student Dummy");
    await page.type('input[name="email"]', "studentdummy@example.com");
    await page.type('input[name="password"]', "password");
    await page.select('select[name="role"]', "student");
    await takeScreenshot(page, "admin_create_dummy_user_form_filled", "form");
    await Promise.all([
      page.waitForNavigation(),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);

    // Delete Dummy User
    console.log("🗑️ Deleting Dummy User...");
    await page.goto(`${BASE_URL}/admin/users`);
    const deleteForms = await page.$$('form[action="/admin/users/delete"]');
    if (deleteForms.length > 0) {
      const lastForm = deleteForms[0];
      const deleteBtn = await lastForm.$('button[type="submit"]');
      await Promise.all([page.waitForNavigation(), deleteBtn.click()]);
      await takeScreenshot(page, "admin_delete_user_success", "success");
    }

    // Create a Dummy Course to Delete
    console.log("📚 Creating Dummy Course for Deletion...");
    await page.goto(`${BASE_URL}/admin/courses/create`);
    await page.type('input[name="name"]', "Dummy Delete Course");
    await page.type('input[name="code"]', "DEL-999");
    await page.evaluate(() => {
      document.querySelector('input[name="credits"]').value = "3";
    });
    const deptOpts2 = await page.$$eval(
      'select[name="department_id"] option',
      (options) => options.map((o) => o.value).filter((v) => v)
    );
    if (deptOpts2.length > 0) {
      await page.select('select[name="department_id"]', deptOpts2[0]);
    }
    await takeScreenshot(page, "admin_create_dummy_course_form_filled", "form");
    await Promise.all([
      page.waitForNavigation(),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);

    // Delete Dummy Course
    console.log("🗑️ Deleting Dummy Course...");
    await page.goto(`${BASE_URL}/admin/courses`);
    const courseDeleteForms = await page.$$(
      'form[action="/admin/courses/delete"]'
    );
    if (courseDeleteForms.length > 0) {
      const lastForm = courseDeleteForms[0];
      const deleteBtn = await lastForm.$('button[type="submit"]');
      await Promise.all([page.waitForNavigation(), deleteBtn.click()]);
      await takeScreenshot(page, "admin_delete_course_success", "success");
    }

    // Logout Admin
    console.log("🔓 Logging out Admin...");
    await page.goto(`${BASE_URL}/logout`);

    // --- 2. TEACHER FLOW ---
    console.log("🔑 Logging in as Teacher...");
    await page.type('input[name="email"]', "teacher1@example.com");
    await page.type('input[name="password"]', "password");
    await takeScreenshot(page, "teacher_login_form_filled", "form");
    await Promise.all([
      page.waitForNavigation(),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);
    await takeScreenshot(page, "teacher_dashboard", "view");

    // Teacher Settings (Assign Course)
    console.log("⚙️ Assigning Course to Teacher...");
    await page.goto(`${BASE_URL}/settings`);
    await takeScreenshot(page, "teacher_settings_view", "view"); // settings is technically a view/form, we'll mark it view

    const assignedCourseId = "1";
    await page.evaluate((courseId) => {
      const hiddenContainer = document.getElementById(
        "teacher-hidden-container"
      );
      if (hiddenContainer) {
        hiddenContainer.innerHTML =
          '<input type="checkbox" name="subjects[]" value="' +
          courseId +
          '" checked>';
        hiddenContainer.closest("form").submit();
      }
    }, assignedCourseId);

    // Check if it submitted
    try {
      await page.waitForNavigation({ timeout: 2000 });
      await takeScreenshot(page, "teacher_course_assigned", "success");
    } catch (e) {
      // Ignored if it didn't navigate
    }

    // Go to courses
    console.log("📚 Accessing Course as Teacher...");
    await page.goto(`${BASE_URL}/course?id=1`);
    await takeScreenshot(page, "teacher_course_view", "view");

    // Add Material
    console.log("➕ Adding Course Material...");
    await page.evaluate(() => openModal("materialModal"));
    await delay(500);
    await page.type(
      '#materialModal input[name="title"]',
      "Lecture 1: Intro Slides"
    );
    const fileInputMat = await page.$('#materialModal input[type="file"]');
    if (fileInputMat) {
      await fileInputMat.uploadFile(dummyFilePath);
    }
    await takeScreenshot(page, "teacher_add_material_modal_filled", "modal");
    await Promise.all([
      page.waitForNavigation(),
      page.evaluate(() =>
        document.querySelector("#materialModal form").submit()
      ),
    ]);
    await takeScreenshot(page, "teacher_material_added", "success");

    // Add Assignment
    console.log("📝 Adding Assignment...");
    await page.evaluate(() => openModal("assignmentModal"));
    await delay(500);
    await page.type(
      '#assignmentModal input[name="title"]',
      "Homework 1: Fundamentals"
    );
    await page.type(
      '#assignmentModal textarea[name="description"]',
      "Complete exercises 1-5."
    );
    await page.evaluate(() => {
      document.querySelector('#assignmentModal input[name="due_date"]').value =
        "2026-12-31T23:59";
    });
    const fileInputAsg = await page.$('#assignmentModal input[type="file"]');
    if (fileInputAsg) {
      await fileInputAsg.uploadFile(dummyFilePath);
    }
    await takeScreenshot(page, "teacher_add_assignment_modal_filled", "modal");
    await Promise.all([
      page.waitForNavigation(),
      page.evaluate(() =>
        document.querySelector("#assignmentModal form").submit()
      ),
    ]);
    await takeScreenshot(page, "teacher_assignment_added", "success");

    // Add Exam
    console.log("✍️ Scheduling Exam...");
    await page.evaluate(() => openModal("examModal"));
    await delay(500);
    await page.type('#examModal input[name="title"]', "Midterm Exam");
    await page.evaluate(() => {
      document.querySelector('#examModal input[name="exam_date"]').value =
        "2026-12-15T10:00";
    });
    await page.evaluate(() => {
      document.querySelector(
        '#examModal input[name="duration_minutes"]'
      ).value = "120";
    });
    await takeScreenshot(page, "teacher_add_exam_modal_filled", "modal");
    await Promise.all([
      page.waitForNavigation(),
      page.evaluate(() => document.querySelector("#examModal form").submit()),
    ]);
    await takeScreenshot(page, "teacher_exam_added", "success");

    // Add Quiz
    console.log("❓ Adding Quiz...");
    await page.evaluate(() => openModal("quizModal"));
    await delay(500);
    await page.type('#quizModal input[name="title"]', "Pop Quiz 1");
    await page.type(
      '#quizModal textarea[name="description"]',
      "Short 10-question quiz on Chapter 1."
    );
    await takeScreenshot(page, "teacher_add_quiz_modal_filled", "modal");
    await Promise.all([
      page.waitForNavigation(),
      page.evaluate(() => document.querySelector("#quizModal form").submit()),
    ]);
    await takeScreenshot(page, "teacher_quiz_added", "success");

    // Expand all course section accordions to show populated content, then screenshot
    console.log("📂 Expanding Course Sections (Teacher View)...");
    await page.evaluate(() => {
      document.querySelectorAll(".section-header").forEach((el) => el.click());
    });
    await delay(700);
    await takeScreenshot(page, "teacher_course_overview_populated", "view");

    // Take Attendance
    console.log("📝 Taking Attendance...");
    await page.goto(`${BASE_URL}/course/attendance?id=1`);
    await takeScreenshot(page, "teacher_attendance_view", "view");

    // Enroll student1 in course 1 as teacher (students can't self-enroll)
    console.log("👥 Enrolling student1 in Course as Teacher...");
    await page.goto(`${BASE_URL}/course/manage-enrollments?id=1`);
    await takeScreenshot(page, "teacher_manage_enrollments", "view");
    // The form uses a custom hidden input for student_id — click the correct dropdown item
    const enrollForm = await page.$('form[action="/enrollments/add"]');
    if (enrollForm) {
      await page.evaluate(() => {
        const items = Array.from(document.querySelectorAll('.dropdown-item'));
        const studentItem = items.find(item => item.textContent.includes('student1@example.com')) || items[0];
        if (studentItem) studentItem.click();
      });
      await Promise.all([
        page.waitForNavigation(),
        page.evaluate(() => document.querySelector('form[action="/enrollments/add"]').submit())
      ]);
      await takeScreenshot(page, "teacher_student_enrolled", "success");
    }

    // Logout Teacher
    console.log("🔓 Logging out Teacher...");
    await page.goto(`${BASE_URL}/logout`);

    // --- 3. STUDENT FLOW ---
    console.log("🔑 Logging in as Student...");
    await page.waitForSelector('input[name="email"]');
    await page.evaluate(() => {
      document.querySelector('input[name="email"]').value = "student1@example.com";
      document.querySelector('input[name="password"]').value = "password";
    });
    await takeScreenshot(page, "student_login_form_filled", "form");
    await Promise.all([
      page.waitForNavigation(),
      page.evaluate(() =>
        document.querySelector('form:not([action="/logout"])').submit()
      ),
    ]);
    await takeScreenshot(page, "student_dashboard", "view");

    // View Enrollments page (student1 was enrolled by teacher in teacher flow above)
    console.log("🎓 Enrolling in Course...");
    await page.goto(`${BASE_URL}/enrollments`);
    await takeScreenshot(page, "student_enrollments_view", "view");

    // View Course Overview
    console.log("👀 Viewing Course Content as Student...");
    await page.goto(`${BASE_URL}/course?id=1`);
    await takeScreenshot(page, "student_course_overview", "view");

    // Expand accordions
    console.log("📂 Expanding Course Sections...");
    await page.evaluate(() => {
      document.querySelectorAll(".section-header").forEach((el) => el.click());
    });
    await delay(500);
    await takeScreenshot(page, "student_course_content_expanded", "view");

    // Settings / Profile
    console.log("⚙️ Updating Profile...");
    await page.goto(`${BASE_URL}/settings`);
    await delay(500);

    // Guard: if session was lost and we're on the login page, re-authenticate as student
    const currentUrl = page.url();
    if (currentUrl.includes("/login")) {
      console.log("🔁 Session lost — re-logging in as Student...");
      await page.waitForSelector('input[name="email"]');
      await page.evaluate(() => {
        document.querySelector('input[name="email"]').value = "student1@example.com";
        document.querySelector('input[name="password"]').value = "password";
      });
      await Promise.all([
        page.waitForNavigation(),
        page.evaluate(() => document.querySelector("form").submit()),
      ]);
      await page.goto(`${BASE_URL}/settings`);
      await delay(500);
    }

    // Use evaluate() to set bio value directly — the textarea lives inside a CSS-hidden
    // tab pane so page.type() would fail (it requires the element to be visible/focusable).
    await page.evaluate(() => {
      const bio = document.querySelector('textarea[name="bio"]');
      if (bio)
        bio.value =
          " I am passionate about learning new technologies and software development.";
    });
    await takeScreenshot(page, "student_profile_edit_form_filled", "form");
    await Promise.all([
      page.waitForNavigation(),
      // Submit the general-info form via its unique hidden field — avoids matching the
      // avatar upload form which also uses action="/settings/profile" and appears first in DOM.
      page.evaluate(() =>
        document
          .querySelector('input[name="is_general_profile"]')
          .closest("form")
          .submit()
      ),
    ]);
    await takeScreenshot(page, "student_profile_updated", "success");

    console.log("✅ Automation test suite completed successfully!");
  } catch (error) {
    console.error("❌ Error during automation:", error);
    await takeScreenshot(page, "error_state", "error");
    const html = await page.content();
    fs.writeFileSync(path.join(__dirname, "error_page.html"), html);
    console.log("📄 Saved error page HTML to tests/error_page.html");
  } finally {
    await browser.close();
  }
}

const MODE = "process";
runAutomation();
