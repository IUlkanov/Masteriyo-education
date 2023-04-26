import {
	Divider,
	Skeleton,
	SkeletonCircle,
	SkeletonText,
	Stack,
} from '@chakra-ui/react';
import React from 'react';

const Profile: React.FC = () => {
	const length = [1, 2, 3];
	return (
		<Stack
			direction={{ base: 'column', sm: 'column', md: 'row', lg: 'row' }}
			spacing="6">
			<SkeletonCircle size="40" />
			<Stack direction="column" spacing="20" flex="1" mt="30px !important">
				<SkeletonText noOfLines={1} width="60px" />
				<Stack direction="column" spacing="5">
					{length.map((x) => (
						<Stack key={x}>
							<Skeleton height="30px" />
							<Skeleton height="30px" />
							<Skeleton height="30px" />
							<Divider />
						</Stack>
					))}
				</Stack>
			</Stack>
		</Stack>
	);
};

export default Profile;
