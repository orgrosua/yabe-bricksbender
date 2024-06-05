/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core';

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

/* import specific icons */
import {
    faBook,
    faUserHeadset,
    faEllipsisVertical,
    faFaceSmileHearts,
    faHourglassClock,
    faChevronRight,
    faChevronLeft,
    faRss,
    faXmark,
    faCheck,
    faSpinner,
    faLayerGroup,
} from '@fortawesome/pro-solid-svg-icons';

import {
    faChevronLeft as faChevronLeftRegular,
    faChevronRight as faChevronRightRegular,
} from '@fortawesome/pro-regular-svg-icons';

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
    faFaceSmileHearts,
    faHourglassClock,
    faChevronRight,
    faChevronLeft,
    faRss,
    faXmark,
    faCheck,
    faSpinner,
    faLayerGroup,

    /** far */
    faChevronLeftRegular,
    faChevronRightRegular,

    /** fab */
    faNpm,
    faFacebook,
);

export { library, FontAwesomeIcon };