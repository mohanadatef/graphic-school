import { http, HttpResponse } from 'msw';

const API_BASE = 'http://graphic-school.test/api';

// Mock data
export const mockUsers = [
  { id: 1, name: 'Admin User', email: 'admin@example.com', role: 'admin' },
  { id: 2, name: 'Student User', email: 'student@example.com', role: 'student' },
  { id: 3, name: 'Instructor User', email: 'instructor@example.com', role: 'instructor' },
];

export const mockPayments = [
  {
    id: 1,
    student_id: 2,
    course_id: 1,
    amount: 1000,
    remaining_amount: 500,
    payment_method: 'cash',
    payment_date: '2024-01-15',
    status: 'completed',
    student: { id: 2, name: 'Student User' },
    course: { id: 1, title: 'Graphic Design Basics' },
  },
  {
    id: 2,
    student_id: 2,
    course_id: 2,
    amount: 1500,
    remaining_amount: 0,
    payment_method: 'transfer',
    payment_date: '2024-01-20',
    status: 'completed',
    student: { id: 2, name: 'Student User' },
    course: { id: 2, title: 'Advanced Design' },
  },
];

export const mockTickets = [
  {
    id: 1,
    title: 'Bug in payment system',
    type: 'bug',
    priority: 'high',
    status: 'open',
    user: { id: 1, name: 'Admin User' },
    created_at: '2024-01-15T10:00:00Z',
  },
  {
    id: 2,
    title: 'Feature request',
    type: 'new_feature',
    priority: 'medium',
    status: 'in_progress',
    user: { id: 2, name: 'Student User' },
    created_at: '2024-01-16T10:00:00Z',
  },
];

export const mockFAQs = [
  {
    id: 1,
    question: 'How do I enroll in a course?',
    answer: 'You can enroll by clicking the enroll button on the course page.',
    category: 'general',
    is_active: true,
    sort_order: 1,
  },
  {
    id: 2,
    question: 'What payment methods are accepted?',
    answer: 'We accept cash, bank transfer, and credit cards.',
    category: 'payment',
    is_active: true,
    sort_order: 2,
  },
];

// MSW Handlers
export const handlers = [
  // Auth endpoints
  http.post(`${API_BASE}/login`, async ({ request }) => {
    const body = await request.json();
    if (body.email === 'admin@example.com' && body.password === 'password') {
      return HttpResponse.json({
        success: true,
        message: 'Login successful',
        data: {
          user: mockUsers[0],
          token: 'mock-jwt-token',
        },
      });
    }
    return HttpResponse.json(
      { success: false, message: 'Invalid credentials', errors: {} },
      { status: 401 }
    );
  }),

  http.post(`${API_BASE}/register`, async ({ request }) => {
    const body = await request.json();
    return HttpResponse.json({
      success: true,
      message: 'Registration successful',
      data: {
        user: { ...body, id: 4, role: 'student' },
        token: 'mock-jwt-token',
      },
    });
  }),

  http.post(`${API_BASE}/logout`, () => {
    return HttpResponse.json({ success: true, message: 'Logged out successfully' });
  }),

  // Payments endpoints
  http.get(`${API_BASE}/admin/payments`, ({ request }) => {
    const url = new URL(request.url);
    const page = parseInt(url.searchParams.get('page') || '1');
    const perPage = parseInt(url.searchParams.get('per_page') || '10');
    
    const start = (page - 1) * perPage;
    const end = start + perPage;
    const paginatedPayments = mockPayments.slice(start, end);

    return HttpResponse.json({
      success: true,
      message: 'Payments retrieved successfully',
      data: paginatedPayments,
      meta: {
        pagination: {
          current_page: page,
          per_page: perPage,
          total: mockPayments.length,
          last_page: Math.ceil(mockPayments.length / perPage),
        },
      },
    });
  }),

  http.post(`${API_BASE}/admin/payments`, async ({ request }) => {
    const body = await request.json();
    const newPayment = {
      id: mockPayments.length + 1,
      ...body,
      student: mockUsers.find((u) => u.id === body.student_id),
      course: { id: body.course_id, title: 'Test Course' },
      created_at: new Date().toISOString(),
    };
    mockPayments.push(newPayment);
    return HttpResponse.json({
      success: true,
      message: 'Payment created successfully',
      data: newPayment,
    });
  }),

  http.get(`${API_BASE}/student/payments`, () => {
    return HttpResponse.json({
      success: true,
      message: 'Payments retrieved successfully',
      data: mockPayments.filter((p) => p.student_id === 2),
      meta: {
        totals: {
          total_paid: 2500,
          total_remaining: 500,
        },
        pagination: {
          current_page: 1,
          per_page: 10,
          total: 2,
          last_page: 1,
        },
      },
    });
  }),

  // Tickets endpoints
  http.get(`${API_BASE}/admin/tickets`, () => {
    return HttpResponse.json({
      success: true,
      message: 'Tickets retrieved successfully',
      data: mockTickets,
      meta: {
        pagination: {
          current_page: 1,
          per_page: 10,
          total: mockTickets.length,
          last_page: 1,
        },
      },
    });
  }),

  http.post(`${API_BASE}/admin/tickets`, async ({ request }) => {
    const body = await request.json();
    const newTicket = {
      id: mockTickets.length + 1,
      ...body,
      user: mockUsers[0],
      created_at: new Date().toISOString(),
    };
    mockTickets.push(newTicket);
    return HttpResponse.json({
      success: true,
      message: 'Ticket created successfully',
      data: newTicket,
    });
  }),

  // FAQs endpoints
  http.get(`${API_BASE}/admin/faqs`, () => {
    return HttpResponse.json({
      success: true,
      message: 'FAQs retrieved successfully',
      data: mockFAQs,
      meta: {
        pagination: {
          current_page: 1,
          per_page: 10,
          total: mockFAQs.length,
          last_page: 1,
        },
      },
    });
  }),

  http.post(`${API_BASE}/admin/faqs`, async ({ request }) => {
    const body = await request.json();
    const newFAQ = {
      id: mockFAQs.length + 1,
      ...body,
    };
    mockFAQs.push(newFAQ);
    return HttpResponse.json({
      success: true,
      message: 'FAQ created successfully',
      data: newFAQ,
    });
  }),

  // Media endpoints
  http.get(`${API_BASE}/admin/media`, () => {
    return HttpResponse.json({
      success: true,
      message: 'Media retrieved successfully',
      data: [],
      meta: {
        pagination: {
          current_page: 1,
          per_page: 24,
          total: 0,
          last_page: 1,
        },
      },
    });
  }),

  // Audit logs endpoints
  http.get(`${API_BASE}/admin/audit-logs`, () => {
    return HttpResponse.json({
      success: true,
      message: 'Audit logs retrieved successfully',
      data: [
        {
          id: 1,
          action: 'created',
          model_type: 'App\\Models\\User',
          model_id: 1,
          user: mockUsers[0],
          description: 'User created',
          created_at: '2024-01-15T10:00:00Z',
        },
      ],
      meta: {
        pagination: {
          current_page: 1,
          per_page: 20,
          total: 1,
          last_page: 1,
        },
      },
    });
  }),

  // Enrollments endpoint (for payment form)
  http.get(`${API_BASE}/admin/enrollments`, () => {
    return HttpResponse.json({
      success: true,
      data: [
        {
          id: 1,
          student_id: 2,
          course_id: 1,
          student: mockUsers[1],
          course: { id: 1, title: 'Graphic Design Basics' },
        },
      ],
    });
  }),
];

