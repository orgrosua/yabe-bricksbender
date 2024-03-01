/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core';

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

/* import specific icons */
import {
    faBook,
    faUserHeadset,
    faEllipsisVertical,
} from '@fortawesome/pro-solid-svg-icons';

import {
    faNpm,
    faFacebook,
} from '@fortawesome/free-brands-svg-icons';

/* add icons to the library */
library.add(
    /** fas */
    faBook,
    faUserHeadset,
    faEllipsisVertical,

    /** far */

    /** fab */
    faNpm,
    faFacebook,
);

export { library, FontAwesomeIcon };