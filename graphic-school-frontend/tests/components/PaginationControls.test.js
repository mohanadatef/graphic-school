import { describe, it, expect } from 'vitest';
import { mountWithPlugins } from '../utils/test-utils';
import PaginationControls from '../../src/components/common/PaginationControls.vue';

describe('PaginationControls', () => {
  const defaultMeta = {
    current_page: 2,
    per_page: 10,
    total: 50,
    last_page: 5,
  };

  it('should render pagination info', () => {
    const wrapper = mountWithPlugins(PaginationControls, {
      props: {
        meta: defaultMeta,
      },
    });

    expect(wrapper.text()).toContain('2');
    expect(wrapper.text()).toContain('5');
    expect(wrapper.text()).toContain('50');
  });

  it('should disable previous button on first page', () => {
    const wrapper = mountWithPlugins(PaginationControls, {
      props: {
        meta: { ...defaultMeta, current_page: 1 },
      },
    });

    const prevButton = wrapper.findAll('button')[0];
    expect(prevButton.attributes('disabled')).toBeDefined();
  });

  it('should disable next button on last page', () => {
    const wrapper = mountWithPlugins(PaginationControls, {
      props: {
        meta: { ...defaultMeta, current_page: 5 },
      },
    });

    const buttons = wrapper.findAll('button');
    const nextButton = buttons[buttons.length - 1];
    expect(nextButton.attributes('disabled')).toBeDefined();
  });

  it('should emit change-page when clicking next', async () => {
    const wrapper = mountWithPlugins(PaginationControls, {
      props: {
        meta: defaultMeta,
      },
    });

    const buttons = wrapper.findAll('button');
    const nextButton = buttons[buttons.length - 1];
    await nextButton.trigger('click');

    expect(wrapper.emitted('change-page')).toBeTruthy();
    expect(wrapper.emitted('change-page')[0]).toEqual([3]);
  });

  it('should emit change-page when clicking previous', async () => {
    const wrapper = mountWithPlugins(PaginationControls, {
      props: {
        meta: defaultMeta,
      },
    });

    const prevButton = wrapper.findAll('button')[0];
    await prevButton.trigger('click');

    expect(wrapper.emitted('change-page')).toBeTruthy();
    expect(wrapper.emitted('change-page')[0]).toEqual([1]);
  });

  it('should emit change-per-page when selecting different per page', async () => {
    const wrapper = mountWithPlugins(PaginationControls, {
      props: {
        meta: defaultMeta,
        perPageOptions: [10, 20, 50],
      },
    });

    const select = wrapper.find('select');
    await select.setValue('20');

    expect(wrapper.emitted('change-per-page')).toBeTruthy();
    expect(wrapper.emitted('change-per-page')[0]).toEqual([20]);
  });

  it('should not emit change-page when clicking disabled button', async () => {
    const wrapper = mountWithPlugins(PaginationControls, {
      props: {
        meta: { ...defaultMeta, current_page: 1 },
      },
    });

    const prevButton = wrapper.findAll('button')[0];
    await prevButton.trigger('click');

    expect(wrapper.emitted('change-page')).toBeFalsy();
  });
});

