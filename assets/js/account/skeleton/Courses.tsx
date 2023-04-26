import {
	Box,
	SimpleGrid,
	Skeleton,
	SkeletonText,
	Stack,
} from '@chakra-ui/react';
import React from 'react';

const Courses: React.FC = () => {
	return (
		<Stack direction="column" spacing="8">
			<SkeletonText noOfLines={1} w="5rem" />
			<SimpleGrid columns={{ base: 1, sm: 1, md: 2, lg: 3 }} spacing="6">
				{[1, 2, 3, 4, 6, 7].map((x) => (
					<Box key={x} borderWidth="1px" borderColor="gray.100">
						<Stack direction="column" spacing="4" w="100%">
							<Skeleton w="100%" h="15rem" />
							<SkeletonText w="90%" spacing="4" alignSelf={'center'} pb="4" />
						</Stack>
					</Box>
				))}
			</SimpleGrid>
		</Stack>
	);
};

export default Courses;
