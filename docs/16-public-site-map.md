# 🌐 Public Site Map - Graphic School

## خريطة الموقع العام

هذا الملف يوثق جميع صفحات الموقع العام والروابط العامة.

---

## 🏠 Home Page

**Route**: `/`  
**Component**: `HomePage.vue`  
**Access**: Public

### Sections:
1. **Hero Section**
   - Main heading
   - Description
   - CTA buttons
   - Statistics

2. **Slider Section**
   - Dynamic sliders from CMS
   - Auto-rotating carousel

3. **Featured Courses Section**
   - Grid of featured courses
   - Course cards with image, title, price, rating

4. **Categories Section**
   - List of course categories
   - Category cards

5. **Testimonials Section**
   - Student testimonials
   - Ratings and comments

6. **Instructors Section**
   - Featured instructors
   - Instructor cards

7. **CTA Section**
   - Call-to-action for enrollment

8. **Footer**
   - Links
   - Contact info
   - Social media links

---

## 📚 Courses Page

**Route**: `/courses`  
**Component**: `CoursesPage.vue`  
**Access**: Public

### Features:
- **Search Bar**
  - Search courses by title, description
- **Filters**
  - Filter by category
  - Filter by price range
  - Filter by delivery type
- **Courses Grid**
  - Paginated course cards
  - Each card shows:
    - Course image
    - Title
    - Category
    - Price
    - Rating
    - Number of students
    - Instructor name
- **Pagination**
  - Page navigation
  - Results count

---

## 🎓 Course Details Page

**Route**: `/courses/:id`  
**Component**: `CourseDetailsPage.vue`  
**Access**: Public

### Sections:
1. **Course Header**
   - Large course image
   - Title
   - Category
   - Rating
   - Price
   - Enroll button (for students)

2. **Course Tabs**:
   - **Overview Tab**
     - Full description
     - Course information (sessions, duration, delivery type)
     - Requirements
   - **Curriculum Tab**
     - List of modules
     - Each module shows:
       - Module title
       - Number of lessons
       - Lesson list (for enrolled students)
   - **Instructors Tab**
     - List of instructors
     - Instructor profiles
   - **Reviews Tab**
     - Student reviews
     - Ratings
     - Comments

3. **Enrollment Section**
   - Enroll button
   - Course price
   - Payment options

---

## 👨‍🏫 Instructors Page

**Route**: `/instructors`  
**Component**: `InstructorsPage.vue`  
**Access**: Public

### Features:
- **Instructor Grid**
  - List of all instructors
  - Each card shows:
    - Instructor image
    - Name
    - Bio
    - Number of courses
    - Rating
- **Search & Filter**
  - Search by name
  - Filter by specialization

---

## 👨‍🏫 Instructor Details Page

**Route**: `/instructors/:id`  
**Component**: `InstructorDetailsPage.vue`  
**Access**: Public

### Sections:
1. **Instructor Header**
   - Large image
   - Name
   - Title
   - Bio

2. **Statistics**
   - Number of courses
   - Number of students
   - Average rating

3. **Courses Section**
   - List of instructor's courses
   - Course cards

4. **Reviews Section**
   - Student reviews
   - Ratings

---

## 📋 Programs Page

**Route**: `/programs`  
**Component**: `PublicPrograms.vue`  
**Access**: Public

### Features:
- **Programs Grid**
  - List of all programs
  - Program cards with:
    - Image
    - Title
    - Description
    - Duration
    - Number of courses

---

## 📋 Program Details Page

**Route**: `/programs/:slug`  
**Component**: `PublicProgramDetails.vue`  
**Access**: Public

### Sections:
1. **Program Header**
   - Image
   - Title
   - Description

2. **Program Information**
   - Duration
   - Courses included
   - Batches available

3. **Enrollment Section**
   - Enroll button
   - Batch selection

---

## 📝 Enrollment Form

**Route**: `/enroll`  
**Component**: `PublicEnrollmentForm.vue`  
**Access**: Public

### Features:
- **Form Fields**:
  - Student name
  - Email
  - Phone
  - Course/Program selection
  - Payment method
- **Validation**
- **Submission**

---

## 🎓 Certificate Verification

**Route**: `/certificate/verify`  
**Component**: `CertificateVerification.vue`  
**Access**: Public

### Features:
- **Verification Form**
  - Certificate number input
  - Verification code input
- **Verification Result**
  - Certificate details
  - Student name
  - Course name
  - Issue date

---

## ℹ️ About Page

**Route**: `/about`  
**Component**: `AboutPage.vue`  
**Access**: Public

### Sections:
1. **About Section**
   - Academy description
   - Mission
   - Vision

2. **Team Section**
   - Key team members

3. **Statistics**
   - Number of students
   - Number of courses
   - Success rate

---

## 📧 Contact Page

**Route**: `/contact`  
**Component**: `ContactPage.vue`  
**Access**: Public

### Features:
- **Contact Form**
  - Name
  - Email
  - Phone
  - Subject
  - Message
- **Contact Information**
  - Address
  - Phone
  - Email
  - Social media links

---

## 🔐 Login Page

**Route**: `/login`  
**Component**: `LoginPage.vue`  
**Access**: Guest only

### Features:
- **Login Form**
  - Email
  - Password
  - Remember me
  - Forgot password link
- **Redirect Logic**
  - After login, redirect based on role:
    - Admin → `/dashboard/admin`
    - Instructor → `/dashboard/instructor`
    - Student → `/` (home)

---

## 📝 Register Page

**Route**: `/register`  
**Component**: `RegisterPage.vue`  
**Access**: Guest only

### Features:
- **Registration Form**
  - Name
  - Email
  - Password
  - Password confirmation
  - Phone
  - Address
- **Validation**
- **Auto-login after registration**
- **Redirect to home**

---

## ⚙️ Setup Wizard

**Route**: `/setup`  
**Component**: `SetupWizard.vue`  
**Access**: Public (when website not activated)

### Steps:
1. **General Information**
   - Academy name
   - Country
   - Default language
   - Timezone
   - Default currency

2. **Branding**
   - Logo
   - Colors
   - Fonts
   - Theme

3. **Website Pages**
   - Homepage template
   - Enable/disable pages

4. **Email Setup**
   - SMTP configuration

5. **Payment Setup**
   - Payment gateway configuration

6. **Launch**
   - Review settings
   - Complete setup

---

## 🚫 404 Page

**Route**: `/:pathMatch(.*)*`  
**Component**: `NotFound.vue`  
**Access**: Public

### Features:
- **404 Message**
- **Back to home button**
- **Search suggestions**

---

## 🔗 Navigation Structure

### Header Navigation:
- Home
- Courses
- Programs
- Instructors
- About
- Contact
- Login/Register (if not authenticated)
- Dashboard (if authenticated)

### Footer Links:
- About
- Courses
- Contact
- Privacy Policy
- Terms of Service
- Social Media Links

---

## 📱 Responsive Design

### Breakpoints:
- **Mobile**: < 640px
- **Tablet**: 640px - 1024px
- **Desktop**: > 1024px

### Mobile Features:
- Hamburger menu
- Collapsible sections
- Touch-friendly buttons
- Optimized images

---

## 🎨 Dynamic Content

### CMS-Driven Pages:
- Homepage sliders
- Testimonials
- Featured courses
- Page builder pages

### Dynamic Routes:
- `/p/{academy_slug}/{page_slug}` - Page builder pages

---

**آخر تحديث**: 2025-01-27  
**الإصدار**: 1.0.0

