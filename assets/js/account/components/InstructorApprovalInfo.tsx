import { Alert, AlertIcon } from '@chakra-ui/react';
import { __ } from '@wordpress/i18n';
import React from 'react';

const InstructorApprovalInfo = () => {
	return (
		<Alert status="warning">
			<AlertIcon />
			{__(
				'You need to be approved by the administrator before you can create courses.',
				'masteriyo'
			)}
		</Alert>
	);
};

export default InstructorApprovalInfo;
