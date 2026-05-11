<div class="tw:w-full">
    <div class="tw:h-[40px] tw:w-[calc(100%-26px)] tw:bg-[#f5f5f5] tw:text-[1.2rem] tw:font-bold tw:leading-[40px] tw:px-[10px]">物件マスター</div>
    <div class="tw:pt-[26px]">
        <div class="tw:px-[26px]">
            <div class="tw:flex">
                <div class="tw:w-[780px]">
                    <div class="tw:h-[26px] tw:w-[160px] tw:leading-[26px] tw:px-[10px] tw:bg-[#d9d9d9] tw:rounded-[5px]">管理契約書SharePoint</div>
                </div>
                <x-button.blue class="tw:!h-[26px] tw:!min-w-[78px] tw:!px-0">登録</x-button.blue>
            </div>
            <div class="tw:mt-[20px] tw:flex">
                <div class="tw:w-[650px] tw:flex tw:gap-x-[26px]">
                    <div>
                        <div class="tw:h-[26px] tw:leading-[26px]">物件ID</div>
                        <x-form.input name="investment_id" value="123" class="tw:!w-[130px]" readonly />
                    </div>
                    <div>
                        <div class="tw:h-[26px] tw:leading-[26px]">都市格</div>
                        <x-form.select-search name="city_rank" :options="$cityRankOptions" class="tw:!w-[130px]" />
                    </div>
                </div>
                <div class="">
                    <div class="tw:h-[26px] tw:leading-[26px]">管理受託日</div>
                    <x-form.input-date name="investment_id" value="123" class="tw:!w-[182px]" />
                </div>
            </div>
            <div class="tw:mt-[10px] tw:flex tw:gap-x-[26px]">
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">物件名</div>
                    <x-form.input name="investment_id" value="123" class="tw:!w-[442px]"  />
                </div>
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">構造（階数）</div>
                    <x-form.input name="investment_id" value="123" class="tw:!w-[364px]"  />
                </div>
            </div>
            <div class="tw:mt-[10px] tw:flex tw:gap-x-[26px]">
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">住居表示</div>
                    <x-form.input name="investment_id" value="123" class="tw:!w-[442px]"  />
                </div>
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">築年数</div>
                    <x-form.input-date name="investment_id" value="123" class="tw:!w-[208px]"  />
                </div>
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">総戸数</div>
                    <x-form.input-unit name="investment_id" value="123" class="tw:!w-[130px]" textClass="tw:!text-right" unit="戸" />
                </div>
            </div>
        </div>
        <div class="tw:px-[26px] tw:mt-[20px]">
            <div class="tw:w-[858px] tw:border-b tw:border-[#d9d9d9]">
                契約管理
            </div>
            <div class="tw:mt-[20px] tw:flex tw:gap-x-[26px]">
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">管理プラン</div>
                    <x-form.select-search name="investment_id" value="123" class="tw:!w-[130px]"  />
                </div>
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">管理料</div>
                    <x-form.input-number-unit name="investment_id" value="123" class="tw:!w-[114px]" textClass="tw:!text-right" unit="%" />
                </div>
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">募集料</div>
                    <x-form.input-number-unit name="investment_id" value="123" class="tw:!w-[114px]" textClass="tw:!text-right" unit="%" />
                </div>
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">更新料</div>
                    <x-form.input-number-unit name="investment_id" value="123" class="tw:!w-[114px]" textClass="tw:!text-right" unit="%" />
                </div>
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">緊急</div>
                    <x-form.input-number-unit name="investment_id" value="123" class="tw:!w-[114px]" textClass="tw:!text-right" unit="円" />
                </div>
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">システム</div>
                    <x-form.input-number-unit name="investment_id" value="123" class="tw:!w-[114px]" textClass="tw:!text-right" unit="円" />
                </div>
            </div>
            <div class="tw:mt-[20px] tw:flex tw:gap-x-[26px]">
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">清掃プラン</div>
                    <x-form.select-search name="investment_id" value="123" class="tw:!w-[130px]"  />
                </div>
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">清掃料</div>
                    <x-form.input-number-unit name="investment_id" value="123" class="tw:!w-[114px]" textClass="tw:!text-right" unit="円" />
                </div>
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">ゴミオプション</div>
                    <x-form.input-number-unit name="investment_id" value="123" class="tw:!w-[114px]" textClass="tw:!text-right" unit="円" />
                </div>
            </div>
            <div class="tw:mt-[20px] tw:flex tw:gap-x-[26px]">
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">建物保守プラン</div>
                    <x-form.select-search name="investment_id" value="123" class="tw:!w-[130px]"  />
                </div>
                <div>
                    <div class="tw:h-[26px] tw:leading-[26px]">保守料金</div>
                    <x-form.input-number-unit name="investment_id" value="123" class="tw:!w-[114px]" textClass="tw:!text-right" unit="円" />
                </div>
            </div>
        </div>
        <div class="tw:mt-[20px]">
            <div class="tw:w-[858px] tw:mt-[20px] tw:mx-[26px] tw:flex tw:justify-between tw:border-b tw:border-[#d9d9d9]">
                <div>交通</div>
            </div>
            @php
                $nearestStationNumbers = ['①', '②', '③', '④', '⑤', '⑥', '⑦', '⑧', '⑨', '⑩'];
            @endphp
            @foreach ($nearestStations as $index => $nearestStation)
                <div class="{{ $index === 0 ? 'tw:mt-[20px]' : 'tw:mt-[10px]' }} tw:flex" wire:key="{{ $nearestStation['_key'] }}">
                    <div class="tw:w-[26px]">
                        @if ($index === 0)
                            <div class="tw:h-[26px]">　</div>
                        @endif
                        <div class="tw:h-[35px] tw:leading-[35px] tw:text-center tw:text-[1.2rem]">
                            {{ $nearestStationNumbers[$index] ?? $index + 1 }}
                        </div>
                    </div>
                    <div class="tw:flex tw:gap-x-[26px]">
                        <div>
                            @if ($index === 0)
                                <div class="tw:h-[26px] tw:leading-[26px]">鉄道名</div>
                            @endif
                            <x-form.input name="investment_nearest_stations[{{ $index }}][railway_name]" wire:model="nearestStations.{{ $index }}.railway_name" class="tw:!w-[234px]" />
                        </div>
                        <div>
                            @if ($index === 0)
                                <div class="tw:h-[26px] tw:leading-[26px]">路線名</div>
                            @endif
                            <x-form.input name="investment_nearest_stations[{{ $index }}][line_name]" wire:model="nearestStations.{{ $index }}.line_name" class="tw:!w-[234px]" />
                        </div>
                        <div>
                            @if ($index === 0)
                                <div class="tw:h-[26px] tw:leading-[26px]">最寄り駅</div>
                            @endif
                            <x-form.input name="investment_nearest_stations[{{ $index }}][station_name]" wire:model="nearestStations.{{ $index }}.station_name" class="tw:!w-[182px]" />
                        </div>
                        <div>
                            @if ($index === 0)
                                <div class="tw:h-[26px] tw:leading-[26px]">徒歩（分）</div>
                            @endif
                            <x-form.input-number name="investment_nearest_stations[{{ $index }}][walking_minutes]" wire:model="nearestStations.{{ $index }}.walking_minutes" class="tw:!w-[104px] tw:text-right" />
                        </div>
                        <div class="tw:w-[26px]">
                            @if ($index === 0)
                                <button type="button" class="tw:h-[26px] tw:text-[1.2rem] tw:border-0 tw:bg-transparent tw:p-0" wire:click="addNearestStation">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                            @endif
                            <button type="button" class="tw:h-[35px] tw:leading-[35px] tw:text-[1.2rem] tw:text-[#ff0000] tw:border-0 tw:bg-transparent tw:p-0" wire:click="removeNearestStation({{ $index }})">
                                <i class="fas fa-minus-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
            @foreach ($nearestBusStops as $index => $nearestBusStop)
                <div class="{{ $index === 0 ? 'tw:mt-[20px]' : 'tw:mt-[10px]' }} tw:flex" wire:key="{{ $nearestBusStop['_key'] }}">
                    <div class="tw:w-[26px]">
                        @if ($index === 0)
                            <div class="tw:h-[26px]">　</div>
                        @endif
                        <div class="tw:h-[35px] tw:leading-[35px] tw:text-center tw:text-[1.2rem]">
                            {{ $nearestStationNumbers[$index] ?? $index + 1 }}
                        </div>
                    </div>
                    <div class="tw:flex tw:gap-x-[26px]">
                        <div class="tw:w-[234px]">
                            @if ($index === 0)
                                <div class="tw:h-[26px] tw:leading-[26px]">バス停留所名</div>
                            @endif
                            <x-form.input name="investment_nearest_bus_stops[{{ $index }}][bus_stop_name]" wire:model="nearestBusStops.{{ $index }}.bus_stop_name" class="tw:!w-[234px]" />
                        </div>
                        <div class="{{ $index === 0 ? 'tw:w-[208px]' : 'tw:w-[104px]' }}">
                            @if ($index === 0)
                                <div class="tw:h-[26px] tw:leading-[26px]">徒歩（分）</div>
                            @endif
                            <x-form.input-number name="investment_nearest_bus_stops[{{ $index }}][walking_minutes]" wire:model="nearestBusStops.{{ $index }}.walking_minutes" class="tw:!w-[104px] tw:text-right" />
                        </div>
                        <div class="tw:w-[234px]">
                            @if ($index === 0)
                                <div class="tw:h-[26px] tw:leading-[26px]">最寄り沿線名</div>
                            @endif
                            <x-form.input name="investment_nearest_bus_stops[{{ $index }}][nearest_line_name]" wire:model="nearestBusStops.{{ $index }}.nearest_line_name" class="tw:!w-[234px]" />
                        </div>
                        <div class="tw:w-[182px]">
                            @if ($index === 0)
                                <div class="tw:h-[26px] tw:leading-[26px]">最寄り駅</div>
                            @endif
                            <x-form.input name="investment_nearest_bus_stops[{{ $index }}][nearest_station_name]" wire:model="nearestBusStops.{{ $index }}.nearest_station_name" class="tw:!w-[182px]" />
                        </div>
                        <div>
                            @if ($index === 0)
                                <div class="tw:flex tw:gap-x-[26px]">
                                    <div class="tw:h-[26px] tw:leading-[26px] tw:relative">
                                        バス所要時間（バス停～駅
                                    </div>
                                    <button type="button" class="tw:h-[26px] tw:text-[1.2rem] tw:border-0 tw:bg-transparent tw:p-0" wire:click="addNearestBusStop">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                </div>
                            @endif
                            <div class="tw:flex tw:gap-x-[26px]">
                                <x-form.input-number name="investment_nearest_bus_stops[{{ $index }}][bus_minutes_to_station]" wire:model="nearestBusStops.{{ $index }}.bus_minutes_to_station" class="tw:!w-[104px] tw:text-right" />
                                <button type="button" class="tw:h-[35px] tw:leading-[35px] tw:text-[1.2rem] tw:text-[#ff0000] tw:border-0 tw:bg-transparent tw:p-0" wire:click="removeNearestBusStop({{ $index }})">
                                    <i class="fas fa-minus-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="tw:mt-[20px]">
            <div class="tw:w-[858px] tw:mt-[20px] tw:mx-[26px] tw:border-b tw:border-[#d9d9d9]">
                タイプ
            </div>
            @foreach ($floorPlans as $index => $floorPlan)
                <div class="{{ $index === 0 ? 'tw:mt-[20px]' : 'tw:mt-[10px]' }} tw:flex" wire:key="{{ $floorPlan['_key'] }}">
                    <div class="tw:w-[26px]">
                        @if ($index === 0)
                            <div class="tw:h-[26px]">　</div>
                        @endif
                        <div class="tw:h-[35px] tw:leading-[35px] tw:text-center tw:text-[1.2rem]">
                            {{ $nearestStationNumbers[$index] ?? $index + 1 }}
                        </div>
                    </div>
                    <div class="tw:flex tw:gap-x-[26px]">
                        <div class="tw:w-[234px]">
                            @if ($index === 0)
                                <div class="tw:h-[26px] tw:leading-[26px]">間取り</div>
                            @endif
                            <x-form.input name="investment_floor_plans[{{ $index }}][floor_plan]" wire:model="floorPlans.{{ $index }}.floor_plan" class="tw:!w-[234px]" />
                        </div>
                        <div class="tw:w-[104px]">
                            @if ($index === 0)
                                <div class="tw:h-[26px] tw:leading-[26px]">+S</div>
                            @endif
                            <x-form.input-number name="investment_floor_plans[{{ $index }}][has_service_room]" wire:model="floorPlans.{{ $index }}.has_service_room" class="tw:!w-[104px] tw:text-right" />
                        </div>
                        <div class="tw:w-[104px]">
                            @if ($index === 0)
                                <div class="tw:h-[26px] tw:leading-[26px]">平米数</div>
                            @endif
                            <x-form.input-number name="investment_floor_plans[{{ $index }}][area_sqm]" wire:model="floorPlans.{{ $index }}.area_sqm" class="tw:!w-[104px] tw:text-right" />
                        </div>
                        <div class="tw:w-[26px]">
                            @if ($index === 0)
                                <button type="button" class="tw:h-[26px] tw:text-[1.2rem] tw:border-0 tw:bg-transparent tw:p-0" wire:click="addFloorPlan">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                            @endif
                            <button type="button" class="tw:h-[35px] tw:leading-[35px] tw:text-[1.2rem] tw:text-[#ff0000] tw:border-0 tw:bg-transparent tw:p-0" wire:click="removeFloorPlan({{ $index }})">
                                <i class="fas fa-minus-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
        

</div>
