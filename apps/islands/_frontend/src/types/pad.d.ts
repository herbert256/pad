// The types of what the pages of islands hold - written by pad types islands examples/typed.
// Made from the data itself: run the command again when a page's data changes.

// examples/typed - the variables of its PHP
export interface ExamplesTypedVars {
  title: string;
  navSection: string;
  islandsCss: number;
  example: {
    page: string;
    title: string;
    icon: string;
    lede: string;
    server: unknown[];
    client: unknown[];
    files: unknown[];
    groupTitle: string;
    prev: {
      page: string;
      title: string;
    };
    next: {
      page: string;
      title: string;
    };
  };
  serverChips: unknown[];
  clientChips: unknown[];
  prev: {
    page: string;
    title: string;
  };
  next: {
    page: string;
    title: string;
  };
  sourceFiles: string;
  order: {
    number: number;
    status: string;
    paid: boolean;
    note: null;
    customer: {
      name: string;
      city: string;
    };
    lines: {
      product: string;
      quantity: number;
      price: number;
      gift?: boolean;
    }[];
  };
}

// examples/typed - its JSON answer, ?examples/typed&padFormat=json
export interface ExamplesTypedAnswer {
  order: {
    number: number;
    status: string;
    paid: boolean;
    note: null;
    customer: {
      name: string;
      city: string;
    };
    lines: {
      product: string;
      quantity: number;
      price: number;
      gift?: boolean;
    }[];
  };
}
