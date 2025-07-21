<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6 rounded-t-lg">
                <h1 class="text-3xl font-bold flex items-center">
                    <i class="fas fa-history mr-3"></i>
                    Activity Logs
                </h1>
                <p class="mt-2 opacity-90">Monitor and track all system activities</p>
            </div>

            <!-- Filters and Sorting -->
            <div class="p-6 border-b bg-gray-50">
                <form method="GET" action="{{ route('activity-logs.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Event Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag mr-1"></i>Event
                            </label>
                            <select name="event" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Events</option>
                                @foreach($events as $event)
                                    <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                                        {{ ucfirst($event) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Causer Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-1"></i>Causer Type
                            </label>
                            <select name="causer_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Causers</option>
                                @foreach($causerTypes as $causerType)
                                    <option value="{{ $causerType }}" {{ request('causer_type') == $causerType ? 'selected' : '' }}>
                                        {{ class_basename($causerType) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subject Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-cube mr-1"></i>Performed On
                            </label>
                            <select name="subject_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Subjects</option>
                                @foreach($subjectTypes as $subjectType)
                                    <option value="{{ $subjectType }}" {{ request('subject_type') == $subjectType ? 'selected' : '' }}>
                                        {{ class_basename($subjectType) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort Options -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-sort mr-1"></i>Sort By
                            </label>
                            <div class="flex space-x-2">
                                <select name="sort_by" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Date</option>
                                    <option value="event" {{ $sortBy == 'event' ? 'selected' : '' }}>Event</option>
                                    <option value="causer_type" {{ $sortBy == 'causer_type' ? 'selected' : '' }}>Causer</option>
                                    <option value="subject_type" {{ $sortBy == 'subject_type' ? 'selected' : '' }}>Subject</option>
                                </select>
                                <select name="sort_direction" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="desc" {{ $sortDirection == 'desc' ? 'selected' : '' }}>↓</option>
                                    <option value="asc" {{ $sortDirection == 'asc' ? 'selected' : '' }}>↑</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition duration-200 flex items-center">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('activity-logs.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                            <i class="fas fa-times mr-1"></i>Clear All
                        </a>
                    </div>
                </form>
            </div>

            <!-- Activity Logs Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_direction' => $sortBy == 'created_at' && $sortDirection == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="flex items-center hover:text-blue-600">
                                    Date & Time
                                    @if($sortBy == 'created_at')
                                        <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'event', 'sort_direction' => $sortBy == 'event' && $sortDirection == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="flex items-center hover:text-blue-600">
                                    Event
                                    @if($sortBy == 'event')
                                        <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'causer_type', 'sort_direction' => $sortBy == 'causer_type' && $sortDirection == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="flex items-center hover:text-blue-600">
                                    Causer
                                    @if($sortBy == 'causer_type')
                                        <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'subject_type', 'sort_direction' => $sortBy == 'subject_type' && $sortDirection == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="flex items-center hover:text-blue-600">
                                    Performed On
                                    @if($sortBy == 'subject_type')
                                        <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Properties</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($activities as $activity)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $activity->created_at->format('M d, Y') }}</span>
                                        <span class="text-gray-500 text-xs">{{ $activity->created_at->format('H:i:s') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @switch($activity->event)
                                            @case('created')
                                                bg-green-100 text-green-800
                                                @break
                                            @case('updated')
                                                bg-blue-100 text-blue-800
                                                @break
                                            @case('deleted')
                                                bg-red-100 text-red-800
                                                @break
                                            @default
                                                bg-gray-100 text-gray-800
                                        @endswitch
                                    ">
                                        <i class="fas fa-{{ $activity->event == 'created' ? 'plus' : ($activity->event == 'updated' ? 'edit' : ($activity->event == 'deleted' ? 'trash' : 'circle')) }} mr-1"></i>
                                        {{ ucfirst($activity->event) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $activity->causer ? class_basename($activity->causer_type) : 'System' }}</span>
                                        @if($activity->causer)
                                            <span class="text-gray-500 text-xs">
                                                ID: {{ $activity->causer_id }}
                                                @if(method_exists($activity->causer, 'name') && $activity->causer->name)
                                                    ({{ $activity->causer->name }})
                                                @elseif(method_exists($activity->causer, 'email') && $activity->causer->email)
                                                    ({{ $activity->causer->email }})
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $activity->subject_type ? class_basename($activity->subject_type) : 'N/A' }}</span>
                                        @if($activity->subject_id)
                                            <span class="text-gray-500 text-xs">ID: {{ $activity->subject_id }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs">
                                        {{ $activity->description ?: 'No description' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    @if($activity->properties && $activity->properties->count() > 0)
                                        <button onclick="toggleProperties({{ $activity->id }})" class="text-blue-600 hover:text-blue-800 flex items-center">
                                            <i class="fas fa-eye mr-1"></i>View Properties
                                        </button>
                                        <div id="properties-{{ $activity->id }}" class="hidden mt-2 p-3 bg-gray-100 rounded text-xs">
                                            <pre class="whitespace-pre-wrap">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    @else
                                        <span class="text-gray-400">No properties</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                                        <p class="text-lg font-medium">No activity logs found</p>
                                        <p class="text-sm">Try adjusting your filters or check back later.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($activities->hasPages())
                <div class="px-6 py-4 border-t bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ $activities->total() }} results
                        </div>
                        <div class="flex space-x-2">
                            {{ $activities->appends(request()->query())->links('pagination::simple-tailwind') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleProperties(activityId) {
            const element = document.getElementById('properties-' + activityId);
            element.classList.toggle('hidden');
        }

        // Auto-submit form when filters change
        document.querySelectorAll('select[name="event"], select[name="causer_type"], select[name="subject_type"]').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
</body>
</html>
