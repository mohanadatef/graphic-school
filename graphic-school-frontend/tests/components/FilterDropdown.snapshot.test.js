import { describe, it } from 'vitest';
import { mountWithPlugins } from '../utils/test-utils';
import FilterDropdown from '../../src/components/common/FilterDropdown.vue';

describe('FilterDropdown Snapshot', () => {
  it('should match snapshot', () => {
    const wrapper = mountWithPlugins(FilterDropdown, {
      props: {
        modelValue: '',
        options: [
          { id: 1, name: 'Option 1' },
          { id: 2, name: 'Option 2' },
          { id: 3, name: 'Option 3' },
        ],
        placeholder: 'Select option',
      },
    });

    expect(wrapper.html()).toMatchSnapshot();
  });
});

