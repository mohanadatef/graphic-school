/**
 * @typedef {Object} User
 * @property {number} id
 * @property {string} name
 * @property {string} email
 * @property {string} role_name
 * @property {Object} role
 */

/**
 * @typedef {Object} Course
 * @property {number} id
 * @property {string} title
 * @property {string} description
 * @property {number} price
 * @property {number} category_id
 * @property {Object} category
 * @property {Array} instructors
 */

/**
 * @typedef {Object} Category
 * @property {number} id
 * @property {string} name
 */

/**
 * @typedef {Object} PaginationMeta
 * @property {number} current_page
 * @property {number} last_page
 * @property {number} per_page
 * @property {number} total
 */

/**
 * @typedef {Object} ApiResponse
 * @property {*} data
 * @property {PaginationMeta} pagination
 */

/**
 * @typedef {'admin' | 'instructor' | 'student'} UserRole
 */

export {};

